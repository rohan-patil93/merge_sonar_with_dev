<?php

if (!defined('CEX_NS_MAIN')) {
    define('CEX_NS_MAIN', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
    define('CEX_NS_REL_PKG', 'http://schemas.openxmlformats.org/package/2006/relationships');
    define('CEX_NS_REL_DOC', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
}

function cex_contract_sheet_headers()
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

function cex_location_sheet_headers()
{
    return array('import_ref', 'location_name', 'service_start_date', 'service_end_date');
}

function cex_asset_sheet_headers()
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

function cex_milestone_sheet_headers()
{
    return array('import_ref', 'milestone_name', 'milestone_date', 'milestone_amount');
}

function cex_col_to_index($col)
{
    $n = 0;
    $len = strlen($col);
    for ($i = 0; $i < $len; $i++) {
        $n = $n * 26 + (ord($col[$i]) - 64);
    }
    return $n;
}

function cex_index_to_col($index)
{
    $index = (int) $index;
    if ($index < 1) {
        $index = 1;
    }
    $s = '';
    while ($index > 0) {
        $index--;
        $s = chr(65 + ($index % 26)) . $s;
        $index = (int) floor($index / 26);
    }
    return $s;
}

function cex_cell_value($c, $sharedStrings)
{
    $attrs = $c->attributes();
    $type = (string) ($attrs['t'] ?? '');
    $children = $c->children(CEX_NS_MAIN);
    $val = '';
    if ($type == 'inlineStr') {
        if (isset($children->is) && isset($children->is->t)) {
            $val = (string) $children->is->t;
        }
        return $val;
    }
    if (isset($children->v)) {
        $val = (string) $children->v;
    }
    if ($type == 's' && isset($sharedStrings[(int) $val])) {
        $val = $sharedStrings[(int) $val];
    }
    return $val;
}

function cex_load_shared_strings($z)
{
    $si = $si ?? '';
    $r = $r ?? '';
    $sharedStrings = array();
    $ssXml = $z->getFromName('xl/sharedStrings.xml');
    if (!$ssXml) {
        return $sharedStrings;
    }
    $ss = @simplexml_load_string($ssXml);
    if (!$ss) {
        return $sharedStrings;
    }
    if (count((array)$ss->children(CEX_NS_MAIN)->si) > 0) {
        foreach ($ss->children(CEX_NS_MAIN)->si as $si) {
            if (isset($si->t)) {
                $sharedStrings[] = (string) $si->t;
            } else {
                $text = '';
                if (count((array)$si->children(CEX_NS_MAIN)->r) > 0) {
                    foreach ($si->children(CEX_NS_MAIN)->r as $r) {
                        if (isset($r->t)) {
                            $text .= (string) $r->t;
                        }
                    }
                }
                $sharedStrings[] = $text;
            }
        }
    }
    return $sharedStrings;
}

function cex_get_sheet_list($z)
{
    $wbXml = $z->getFromName('xl/workbook.xml');
    $relsXml = $z->getFromName('xl/_rels/workbook.xml.rels');
    if (!$wbXml || !$relsXml) {
        throw new RuntimeException('Invalid xlsx workbook structure');
    }
    $wb = @simplexml_load_string($wbXml);
    $rels = @simplexml_load_string($relsXml);
    if (!$wb || !$rels) {
        throw new RuntimeException('Unable to parse workbook XML');
    }
    $relMap = array();
    $rels->registerXPathNamespace('pr', CEX_NS_REL_PKG);
    $relNodes = $rels->xpath('//pr:Relationship');
    if (count((array)$relNodes) > 0) {
        foreach ($relNodes as $rel) {
            $relMap[(string) $rel['Id']] = (string) $rel['Target'];
        }
    }
    $sheets = array();
    $wb->registerXPathNamespace('m', CEX_NS_MAIN);
    $wb->registerXPathNamespace('r', CEX_NS_REL_DOC);
    $sheetNodes = $wb->xpath('//m:sheet');
    if (count((array)$sheetNodes) > 0) {
        foreach ($sheetNodes as $sheet) {
            $attrs = $sheet->attributes(CEX_NS_REL_DOC);
            $rid = (string) ($attrs['id'] ?? '');
            $target = $relMap[$rid] ?? '';
            if ($target == '') {
                continue;
            }
            if (strpos($target, 'xl/') !== 0) {
                $target = 'xl/' . ltrim(str_replace('../', '', $target), '/');
            }
            $sheets[] = array(
                'name' => (string) $sheet['name'],
                'path' => $target,
            );
        }
    }
    return $sheets;
}

function cex_parse_sheet_grid($z, $sheetPath, $sharedStrings)
{
    $c = $c ?? '';
    $m = $m ?? '';
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
            $colIdx = 0;
            foreach ($row->children(CEX_NS_MAIN) as $c) {
                if ($c->getName() != 'c') {
                    continue;
                }
                $attrs = $c->attributes();
                $ref = isset($attrs['r']) ? (string) $attrs['r'] : '';
                if ($ref != '' && preg_match('/([A-Z]+)(\d+)/', $ref, $m)) {
                    $colIdx = cex_col_to_index($m[1]);
                } else {
                    $colIdx++;
                }
                $val = cex_cell_value($c, $sharedStrings);
                $grid[$r][$colIdx] = $val;
                if ($colIdx > $maxCol) {
                    $maxCol = $colIdx;
                }
            }
        }
    }
    return array('grid' => $grid, 'max_col' => $maxCol);
}

function cex_normalize_header($header)
{
    $h = strtolower(trim((string) $header));
    $h = preg_replace('/\s+/', '_', $h);
    $h = preg_replace('/[^a-z0-9_]/', '', $h);
    return $h;
}

function cex_grid_to_assoc_rows($grid, $maxCol)
{
    $rows = array();
    if (count((array)$grid) == 0) {
        return $rows;
    }
    ksort($grid, SORT_NUMERIC);
    $headerRowNum = 0;
    $headers = array();
    foreach ($grid as $rn => $cols) {
        $headerRowNum = $rn;
        for ($c = 1; $c <= $maxCol; $c++) {
            $raw = trim((string) ($cols[$c] ?? ''));
            $key = cex_normalize_header($raw);
            if ($key != '') {
                $headers[$c] = $key;
            }
        }
        break;
    }
    if (count((array)$headers) == 0) {
        return $rows;
    }
    foreach ($grid as $rn => $cols) {
        if ($rn == $headerRowNum) {
            continue;
        }
        $row = array();
        $any = false;
        if (count((array)$headers) > 0) {
            foreach ($headers as $c => $key) {
                $val = trim((string) ($cols[$c] ?? ''));
                $row[$key] = $val;
                if ($val != '') {
                    $any = true;
                }
            }
        }
        if ($any) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function cex_find_sheet_by_name($sheets, $wanted)
{
    $sheet = $sheet ?? [];
    $wantedNorm = cex_normalize_header($wanted);
    if (count((array)$sheets) > 0) {
        foreach ($sheets as $sheet) {
            $nameNorm = cex_normalize_header($sheet['name'] ?? '');
            if ($nameNorm == $wantedNorm) {
                return $sheet;
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
function cex_parse_contracts_workbook($xlsxPath)
{
    if (!class_exists('ZipArchive')) {
        throw new RuntimeException('ZipArchive extension is required to read Excel files.');
    }
    $z = new ZipArchive();
    if ($z->open($xlsxPath) !== true) {
        throw new RuntimeException('Unable to open Excel file.');
    }
    $sharedStrings = cex_load_shared_strings($z);
    $sheets = cex_get_sheet_list($z);
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
    if (count((array)$map) > 0) {
        foreach ($map as $key => $names) {
            $found = null;
            if (count((array)$names) > 0) {
                foreach ($names as $n) {
                    $found = cex_find_sheet_by_name($sheets, $n);
                    if ($found != null) {
                        break;
                    }
                }
            }
            if ($found == null) {
                continue;
            }
            $parsed = cex_parse_sheet_grid($z, $found['path'], $sharedStrings);
            $result[$key] = cex_grid_to_assoc_rows($parsed['grid'], $parsed['max_col']);
        }
    }
    $z->close();
    return $result;
}

function cex_xml_escape($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

function cex_sheet_xml_from_rows($rows)
{
    $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
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
                    $ref = cex_index_to_col($col) . $rowNum;
                    $xml .= '<c r="' . $ref . '" t="inlineStr"><is><t>' . cex_xml_escape($cell) . '</t></is></c>';
                }
            }
            $xml .= '</row>';
        }
    }
    $xml .= '</sheetData></worksheet>';
    return $xml;
}

function cex_write_xlsx($sheets, $filepath)
{
    $filepath = $filepath ?? [];
    if (!class_exists('ZipArchive')) {
        throw new RuntimeException('ZipArchive extension is required to write Excel files.');
    }
    if (count((array)$sheets) == 0) {
        throw new RuntimeException('No sheets to write.');
    }
    $dir = dirname($filepath);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    if (file_exists($filepath)) {
        @unlink($filepath);
    }
    $z = new ZipArchive();
    if ($z->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new RuntimeException('Unable to create Excel file.');
    }

    $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
    $contentTypes .= '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">';
    $contentTypes .= '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>';
    $contentTypes .= '<Default Extension="xml" ContentType="application/xml"/>';
    $contentTypes .= '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>';

    $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
    $wbRels .= '<Relationships xmlns="' . CEX_NS_REL_PKG . '">';

    $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
    $workbook .= '<workbook xmlns="' . CEX_NS_MAIN . '" xmlns:r="' . CEX_NS_REL_DOC . '"><sheets>';

    $i = 0;
    if (count((array)$sheets) > 0) {
        foreach ($sheets as $sheet) {
            $i++;
            $sheetName = preg_replace('/[\\\\\/\?\*\[\]:]/', '', (string) ($sheet['name'] ?? ('Sheet' . $i)));
            if ($sheetName == '') {
                $sheetName = 'Sheet' . $i;
            }
            if (strlen($sheetName) > 31) {
                $sheetName = substr($sheetName, 0, 31);
            }
            $rows = $sheet['rows'] ?? array();
            $sheetPath = 'xl/worksheets/sheet' . $i . '.xml';
            $z->addFromString($sheetPath, cex_sheet_xml_from_rows($rows));
            $contentTypes .= '<Override PartName="/' . $sheetPath . '" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
            $rid = 'rId' . $i;
            $wbRels .= '<Relationship Id="' . $rid . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $i . '.xml"/>';
            $workbook .= '<sheet name="' . cex_xml_escape($sheetName) . '" sheetId="' . $i . '" r:id="' . $rid . '"/>';
        }
    }
    $workbook .= '</sheets></workbook>';
    $wbRels .= '</Relationships>';
    $contentTypes .= '</Types>';

    $rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
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

function cex_normalize_date_ymd($value)
{
    $m = $m ?? '';
    $value = trim((string) $value);
    if ($value == '') {
        return '';
    }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        return $value;
    }
    if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/', $value, $m)) {
        return sprintf('%04d-%02d-%02d', (int) $m[3], (int) $m[2], (int) $m[1]);
    }
    $ts = strtotime($value);
    if ($ts !== false) {
        return date('Y-m-d', $ts);
    }
    return '';
}

function cex_normalize_billing_cycle($cycle)
{
    $cycle = strtolower(trim((string) $cycle));
    $cycle = str_replace(array(' ', '-'), '_', $cycle);
    if ($cycle == 'halfyearly' || $cycle == 'half_year' || $cycle == 'semi_annual' || $cycle == 'semiannual') {
        return 'half_yearly';
    }
    if (in_array($cycle, array('monthly', 'quarterly', 'half_yearly', 'yearly'))) {
        return $cycle;
    }
    return $cycle;
}

function cex_generate_billing_milestones($startYmd, $endYmd, $cycle, $billingDay, $totalAmount)
{
    $cycle = cex_normalize_billing_cycle($cycle);
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
    $daysInMonth = (int) $cursor->format('t');
    $cursor->setDate((int) $cursor->format('Y'), (int) $cursor->format('m'), min($billingDay, $daysInMonth));
    if ($cursor < $start) {
        $cursor->modify('+' . $intervalMonths . ' months');
        $daysInMonth = (int) $cursor->format('t');
        $cursor->setDate((int) $cursor->format('Y'), (int) $cursor->format('m'), min($billingDay, $daysInMonth));
    }
    while ($cursor <= $end) {
        $dates[] = $cursor->format('Y-m-d');
        $cursor->modify('+' . $intervalMonths . ' months');
        $daysInMonth = (int) $cursor->format('t');
        $cursor->setDate((int) $cursor->format('Y'), (int) $cursor->format('m'), min($billingDay, $daysInMonth));
    }
    if (count((array)$dates) == 0) {
        $dates[] = $start->format('Y-m-d');
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
            if ($i == $count - 1) {
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
