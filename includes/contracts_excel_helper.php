<?php

defined('CEX_NS_MAIN') || define('CEX_NS_MAIN', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
defined('CEX_NS_REL_PKG') || define('CEX_NS_REL_PKG', 'http://schemas.openxmlformats.org/package/2006/relationships');
defined('CEX_NS_REL_DOC') || define('CEX_NS_REL_DOC', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
defined('CEX_XML_PROLOG') || define('CEX_XML_PROLOG', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>');
defined('CEX_DATE_FORMAT_YMD') || define('CEX_DATE_FORMAT_YMD', 'Y-m-d');

if (!class_exists('ContractsExcelException')) {
    class ContractsExcelException extends RuntimeException
    {
    }
}

function cexContractSheetHeaders()
{
    return array(
        'import_ref',
        'contract_type',
        'contract_name',
        'start_date',
        'end_date',
        'contract_amount',
        'department_name',
        'party_type',
        'party_bp_code',
        'party_name',
        'payment_plan',
        'billing_cycle',
        'billing_day',
        'contract_category',
        'payment_terms',
        'services_csv',
        'locations_csv',
        'agreement_type',
        'description_type',
        'rt_number',
        'invoice_number',
        'business_code',
        'contract_owner_name',
        'contract_owner_email',
        'contract_owner_number',
        'description',
        'additional_notes',
        'termination_condition',
        'customer_connect_name',
        'default_employee_name',
        'is_oem',
        'oem_registration_number',
        'oem_distributor_name',
        'oem_product_category',
    );
}

function cexLocationSheetHeaders()
{
    return array('import_ref', 'location_name', 'service_start_date', 'service_end_date');
}

function cexAssetSheetHeaders()
{
    return array(
        'import_ref',
        'location_name',
        'asset_code',
        'service_frequency',
        'visits_per_interval',
        'service_start_date',
        'service_end_date',
    );
}

function cexMilestoneSheetHeaders()
{
    return array('import_ref', 'milestone_name', 'milestone_date', 'milestone_amount');
}

function cexColToIndex($col)
{
    $n = 0;
    $len = strlen($col);
    for ($i = 0; $i < $len; $i++) {
        $n = $n * 26 + (ord($col[$i]) - 64);
    }
    return $n;
}

function cexIndexToCol($index)
{
    $colIndex = (int) $index;
    if ($colIndex < 1) {
        $colIndex = 1;
    }
    $s = '';
    while ($colIndex > 0) {
        $colIndex--;
        $s = chr(65 + ($colIndex % 26)) . $s;
        $colIndex = (int) floor($colIndex / 26);
    }
    return $s;
}

function cexCellValue($c, $sharedStrings)
{
    $attrs = $c->attributes();
    $type = (string) ($attrs['t'] ?? '');
    $children = $c->children(CEX_NS_MAIN);
    $val = '';
    if ($type === 'inlineStr') {
        if (isset($children->is->t)) {
            $val = (string) $children->is->t;
        }
        return $val;
    }
    if (isset($children->v)) {
        $val = (string) $children->v;
    }
    if ($type === 's' && isset($sharedStrings[(int) $val])) {
        $val = $sharedStrings[(int) $val];
    }
    return $val;
}

function cexExtractSharedStringItem($si)
{
    if (isset($si->t)) {
        return (string) $si->t;
    }
    $text = '';
    $runs = $si->children(CEX_NS_MAIN)->r;
    if (count((array)$runs) > 0) {
        foreach ($runs as $r) {
            if (isset($r->t)) {
                $text .= (string) $r->t;
            }
        }
    }
    return $text;
}

function cexLoadSharedStrings($z)
{
    $sharedStrings = array();
    $ssXml = $z->getFromName('xl/sharedStrings.xml');
    if (!$ssXml) {
        return $sharedStrings;
    }
    $ss = @simplexml_load_string($ssXml);
    if (!$ss) {
        return $sharedStrings;
    }
    $siNodes = $ss->children(CEX_NS_MAIN)->si;
    if (count((array)$siNodes) > 0) {
        foreach ($siNodes as $si) {
            $sharedStrings[] = cexExtractSharedStringItem($si);
        }
    }
    return $sharedStrings;
}

function cexBuildRelationshipMap($rels)
{
    $relMap = array();
    $rels->registerXPathNamespace('pr', CEX_NS_REL_PKG);
    $relNodes = $rels->xpath('//pr:Relationship');
    if (count((array)$relNodes) > 0) {
        foreach ($relNodes as $rel) {
            $relMap[(string) $rel['Id']] = (string) $rel['Target'];
        }
    }
    return $relMap;
}

function cexNormalizeSheetPath($target)
{
    if (strpos($target, 'xl/') !== 0) {
        return 'xl/' . ltrim(str_replace('../', '', $target), '/');
    }
    return $target;
}

function cexGetSheetList($z)
{
    $wbXml = $z->getFromName('xl/workbook.xml');
    $relsXml = $z->getFromName('xl/_rels/workbook.xml.rels');
    if (!$wbXml || !$relsXml) {
        throw new ContractsExcelException('Invalid xlsx workbook structure');
    }
    $wb = @simplexml_load_string($wbXml);
    $rels = @simplexml_load_string($relsXml);
    if (!$wb || !$rels) {
        throw new ContractsExcelException('Unable to parse workbook XML');
    }
    $relMap = cexBuildRelationshipMap($rels);
    $sheets = array();
    $wb->registerXPathNamespace('m', CEX_NS_MAIN);
    $wb->registerXPathNamespace('r', CEX_NS_REL_DOC);
    $sheetNodes = $wb->xpath('//m:sheet');
    if (count((array)$sheetNodes) > 0) {
        foreach ($sheetNodes as $sheet) {
            $attrs = $sheet->attributes(CEX_NS_REL_DOC);
            $rid = (string) ($attrs['id'] ?? '');
            $target = $relMap[$rid] ?? '';
            if ($target === '') {
                continue;
            }
            $sheets[] = array(
                'name' => (string) $sheet['name'],
                'path' => cexNormalizeSheetPath($target),
            );
        }
    }
    return $sheets;
}

function cexParseRowCells($row, $sharedStrings, &$maxCol)
{
    $cells = array();
    $colIdx = 0;
    $m = array();
    foreach ($row->children(CEX_NS_MAIN) as $c) {
        if ($c->getName() !== 'c') {
            continue;
        }
        $attrs = $c->attributes();
        $ref = isset($attrs['r']) ? (string) $attrs['r'] : '';
        if ($ref !== '' && preg_match('/([A-Z]+)(\d+)/', $ref, $m)) {
            $colIdx = cexColToIndex($m[1]);
        } else {
            $colIdx++;
        }
        $val = cexCellValue($c, $sharedStrings);
        $cells[$colIdx] = $val;
        if ($colIdx > $maxCol) {
            $maxCol = $colIdx;
        }
    }
    return $cells;
}

function cexParseSheetGrid($z, $sheetPath, $sharedStrings)
{
    $sheetXml = $z->getFromName($sheetPath);
    if (!$sheetXml) {
        return array('grid' => array(), 'max_col' => 0);
    }
    $sheet = @simplexml_load_string($sheetXml);
    if (!$sheet) {
        return array('grid' => array(), 'max_col' => 0);
    }
    $sheet->registerXPathNamespace('m', CEX_NS_MAIN);
    $rowNodes = $sheet->xpath('//m:sheetData/m:row');
    if (!is_array($rowNodes)) {
        $rowNodes = array();
    }
    $grid = array();
    $maxCol = 0;
    if (count((array)$rowNodes) > 0) {
        foreach ($rowNodes as $row) {
            $r = (int) $row['r'];
            $grid[$r] = cexParseRowCells($row, $sharedStrings, $maxCol);
        }
    }
    return array('grid' => $grid, 'max_col' => $maxCol);
}

function cexNormalizeHeader($header)
{
    $h = strtolower(trim((string) $header));
    $h = preg_replace('/\s+/', '_', $h);
    return preg_replace('/[^a-z0-9_]/', '', $h);
}

function cexExtractGridHeaders($firstRowCols, $maxCol)
{
    $headers = array();
    for ($c = 1; $c <= $maxCol; $c++) {
        $raw = trim((string) ($firstRowCols[$c] ?? ''));
        $key = cexNormalizeHeader($raw);
        if ($key !== '') {
            $headers[$c] = $key;
        }
    }
    return $headers;
}

function cexBuildAssocRow($cols, $headers)
{
    $row = array();
    $hasValue = false;
    foreach ($headers as $c => $key) {
        $val = trim((string) ($cols[$c] ?? ''));
        $row[$key] = $val;
        if ($val !== '') {
            $hasValue = true;
        }
    }
    return $hasValue ? $row : null;
}

function cexGridToAssocRows($grid, $maxCol)
{
    $rows = array();
    if (count((array)$grid) === 0) {
        return $rows;
    }
    ksort($grid, SORT_NUMERIC);
    $headerRowNum = 0;
    $headers = array();
    foreach ($grid as $rn => $cols) {
        $headerRowNum = $rn;
        $headers = cexExtractGridHeaders($cols, $maxCol);
        break;
    }
    if (count((array)$headers) === 0) {
        return $rows;
    }
    foreach ($grid as $rn => $cols) {
        if ($rn === $headerRowNum) {
            continue;
        }
        $assocRow = cexBuildAssocRow($cols, $headers);
        if ($assocRow !== null) {
            $rows[] = $assocRow;
        }
    }
    return $rows;
}

function cexFindSheetByName($sheets, $wanted)
{
    $wantedNorm = cexNormalizeHeader($wanted);
    if (count((array)$sheets) > 0) {
        foreach ($sheets as $sheet) {
            $nameNorm = cexNormalizeHeader($sheet['name'] ?? '');
            if ($nameNorm === $wantedNorm) {
                return $sheet;
            }
        }
    }
    return null;
}

function cexFindSheetByCandidateNames($sheets, $candidateNames)
{
    if (count((array)$candidateNames) > 0) {
        foreach ($candidateNames as $n) {
            $found = cexFindSheetByName($sheets, $n);
            if ($found !== null) {
                return $found;
            }
        }
    }
    return null;
}

/**
 * Parse contracts import workbook into 4 sheet arrays.
 *
 * @return array{contracts:array,locations:array,assets:array,milestones:array}
 */
function cexParseContractsWorkbook($xlsxPath)
{
    if (!class_exists('ZipArchive')) {
        throw new ContractsExcelException('ZipArchive extension is required to read Excel files.');
    }
    $z = new ZipArchive();
    if ($z->open($xlsxPath) !== true) {
        throw new ContractsExcelException('Unable to open Excel file.');
    }
    $sharedStrings = cexLoadSharedStrings($z);
    $sheets = cexGetSheetList($z);
    $result = array(
        'contracts' => array(),
        'locations' => array(),
        'assets' => array(),
        'milestones' => array(),
    );
    $map = array(
        'contracts' => array('Contracts', 'contracts'),
        'locations' => array('Locations', 'locations'),
        'assets' => array('Assets', 'assets'),
        'milestones' => array('Milestones', 'milestones'),
    );
    foreach ($map as $key => $candidateNames) {
        $found = cexFindSheetByCandidateNames($sheets, $candidateNames);
        if ($found === null) {
            continue;
        }
        $parsed = cexParseSheetGrid($z, $found['path'], $sharedStrings);
        $result[$key] = cexGridToAssocRows($parsed['grid'], $parsed['max_col']);
    }
    $z->close();
    return $result;
}

function cexXmlEscape($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

function cexSheetXmlFromRows($rows)
{
    $xml = CEX_XML_PROLOG;
    $xml .= '<worksheet xmlns="' . CEX_NS_MAIN . '"><sheetData>';
    $rowNum = 0;
    if (count((array)$rows) > 0) {
        foreach ($rows as $row) {
            $rowNum++;
            $xml .= '<row r="' . $rowNum . '">';
            $col = 0;
            if (count((array)$row) > 0) {
                foreach ($row as $cell) {
                    $col++;
                    $ref = cexIndexToCol($col) . $rowNum;
                    $xml .= '<c r="' . $ref . '" t="inlineStr"><is><t>' . cexXmlEscape($cell) . '</t></is></c>';
                }
            }
            $xml .= '</row>';
        }
    }
    $xml .= '</sheetData></worksheet>';
    return $xml;
}

function cexSanitizeSheetName($name, $index)
{
    $sheetName = preg_replace('/[\\\\\/\?\*\[\]:]/', '', (string) ($name ?? ('Sheet' . $index)));
    if ($sheetName === '') {
        $sheetName = 'Sheet' . $index;
    }
    if (strlen($sheetName) > 31) {
        $sheetName = substr($sheetName, 0, 31);
    }
    return $sheetName;
}

function cexWriteXlsx($sheets, $filepath)
{
    if (!class_exists('ZipArchive')) {
        throw new ContractsExcelException('ZipArchive extension is required to write Excel files.');
    }
    if (count((array)$sheets) === 0) {
        throw new ContractsExcelException('No sheets to write.');
    }
    $dir = dirname((string)$filepath);
    if (!is_dir($dir)) {
        mkdir($dir, 0750, true);
    }
    if (file_exists($filepath)) {
        @unlink($filepath);
    }
    $z = new ZipArchive();
    if ($z->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new ContractsExcelException('Unable to create Excel file.');
    }

    $contentTypes = CEX_XML_PROLOG;
    $contentTypes .= '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">';
    $contentTypes .= '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>';
    $contentTypes .= '<Default Extension="xml" ContentType="application/xml"/>';
    $contentTypes .= '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>';

    $wbRels = CEX_XML_PROLOG;
    $wbRels .= '<Relationships xmlns="' . CEX_NS_REL_PKG . '">';

    $workbook = CEX_XML_PROLOG;
    $workbook .= '<workbook xmlns="' . CEX_NS_MAIN . '" xmlns:r="' . CEX_NS_REL_DOC . '"><sheets>';

    $i = 0;
    if (count((array)$sheets) > 0) {
        foreach ($sheets as $sheet) {
            $i++;
            $sheetName = cexSanitizeSheetName($sheet['name'] ?? null, $i);
            $rows = $sheet['rows'] ?? array();
            $sheetPath = 'xl/worksheets/sheet' . $i . '.xml';
            $z->addFromString($sheetPath, cexSheetXmlFromRows($rows));
            $contentTypes .= '<Override PartName="/' . $sheetPath . '" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
            $rid = 'rId' . $i;
            $wbRels .= '<Relationship Id="' . $rid . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $i . '.xml"/>';
            $workbook .= '<sheet name="' . cexXmlEscape($sheetName) . '" sheetId="' . $i . '" r:id="' . $rid . '"/>';
        }
    }
    $workbook .= '</sheets></workbook>';
    $wbRels .= '</Relationships>';
    $contentTypes .= '</Types>';

    $rootRels = CEX_XML_PROLOG;
    $rootRels .= '<Relationships xmlns="' . CEX_NS_REL_PKG . '">';
    $rootRels .= '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>';
    $rootRels .= '</Relationships>';

    $z->addFromString('[Content_Types].xml', $contentTypes);
    $z->addFromString('_rels/.rels', $rootRels);
    $z->addFromString('xl/workbook.xml', $workbook);
    $z->addFromString('xl/_rels/workbook.xml.rels', $wbRels);
    $z->close();
    return $filepath;
}

function cexNormalizeDateYmd($value)
{
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }
    $m = array();
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        return $value;
    }
    if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/', $value, $m)) {
        return sprintf('%04d-%02d-%02d', (int) $m[3], (int) $m[2], (int) $m[1]);
    }
    $ts = strtotime($value);
    return ($ts !== false) ? date(CEX_DATE_FORMAT_YMD, $ts) : '';
}

function cexNormalizeBillingCycle($cycle)
{
    $cycle = strtolower(trim((string) $cycle));
    $cycle = str_replace(array(' ', '-'), '_', $cycle);
    if ($cycle === 'halfyearly' || $cycle === 'half_year' || $cycle === 'semi_annual' || $cycle === 'semiannual') {
        return 'half_yearly';
    }
    return $cycle;
}

function cexAlignCursorToBillingDay($cursor, $billingDay)
{
    $daysInMonth = (int) $cursor->format('t');
    $cursor->setDate((int) $cursor->format('Y'), (int) $cursor->format('m'), min($billingDay, $daysInMonth));
}

function cexGenerateBillingMilestones($startYmd, $endYmd, $cycle, $billingDay, $totalAmount)
{
    $cycle = cexNormalizeBillingCycle($cycle);
    $intervalMap = array('monthly' => 1, 'quarterly' => 3, 'half_yearly' => 6, 'yearly' => 12);
    $intervalMonths = $intervalMap[$cycle] ?? 3;
    $start = DateTime::createFromFormat('Y-m-d', $startYmd);
    $end = DateTime::createFromFormat('Y-m-d', $endYmd);
    if (!$start || !$end || $start > $end) {
        return array();
    }
    $billingDay = max(1, min(31, (int) $billingDay));
    $dates = array();
    $cursor = clone $start;
    cexAlignCursorToBillingDay($cursor, $billingDay);
    if ($cursor < $start) {
        $cursor->modify('+' . $intervalMonths . ' months');
        cexAlignCursorToBillingDay($cursor, $billingDay);
    }
    while ($cursor <= $end) {
        $dates[] = $cursor->format(CEX_DATE_FORMAT_YMD);
        $cursor->modify('+' . $intervalMonths . ' months');
        cexAlignCursorToBillingDay($cursor, $billingDay);
    }
    if (count((array)$dates) === 0) {
        $dates[] = $start->format(CEX_DATE_FORMAT_YMD);
    }
    $count = count($dates);
    $priceEach = $count > 0 ? round((float) $totalAmount / $count, 2) : 0;
    $labelMap = array('monthly' => 'Month', 'quarterly' => 'Quarter', 'half_yearly' => 'Half-Year', 'yearly' => 'Year');
    $periodPrefix = $labelMap[$cycle] ?? 'Period';
    $milestones = array();
    $runningTotal = 0;
    if (count((array)$dates) > 0) {
        foreach ($dates as $i => $date) {
            $price = $priceEach;
            if ($i === $count - 1) {
                $price = round((float) $totalAmount - $runningTotal, 2);
            } else {
                $runningTotal += $price;
            }
            $milestones[] = array(
                'milestone_name' => $periodPrefix . ' ' . ($i + 1),
                'milestone_date' => $date,
                'milestone_price' => $price,
            );
        }
    }
    return $milestones;
}
