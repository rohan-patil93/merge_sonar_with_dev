(function ($) {
    const REQUIRED_HEADERS = [
        'PO Available',
        'Financial Year',
        'PO Date',
        'PO Amount',
        'PO Value (Excl. Tax)'
    ];

    const COLUMN_ORDER = [
        'PO Available',
        'Financial Year',
        'PO No',
        'PO Date',
        'PO Amount',
        'PO Value (Excl. Tax)',
        'GST %',
        'PO Value (Incl. Tax)',
        'Business Code',
        'Business Name',
        'Other PO Details',
        'Remarks'
    ];

    let previewRows = [];
    let invalidRows = [];

    $(document).ready(function () {
        const cfgNode = document.getElementById('poUploadConfig');
        if (!cfgNode) {
            return;
        }

        const cfg = cfgNode.dataset || {};
        const sampleHeaders = parseJsonData(cfg.sampleHeaders, COLUMN_ORDER);

        $('#downloadPoSampleBtn').on('click', function () {
            downloadSampleCsv(sampleHeaders);
        });

        $('#triggerPoUploadBtn').on('click', function () {
            $('#poUploadInput').trigger('click');
        });

        $('#poUploadInput').on('change', async function (event) {
            const file = event.target.files[0];
            if (!file) {
                return;
            }

            const extension = getFileExtension(file.name);
            if (!['csv', 'xlsx', 'xls'].includes(extension)) {
                notifyPoUploadError('Only .xlsx, .xls, and .csv files are allowed.');
                this.value = '';
                return;
            }

            try {
                const rows = await parseUploadFile(file, extension);
                if (!rows.length) {
                    notifyPoUploadError('No rows found in the uploaded file.');
                    clearPreview();
                    return;
                }

                const mappedRows = mapRowsFromUpload(rows);
                if (!mappedRows.length) {
                    notifyPoUploadError('The uploaded file does not contain usable row data.');
                    clearPreview();
                    return;
                }

                renderPreview(mappedRows, file.name);
            } catch (error) {
                console.error(error);
                notifyPoUploadError(error.message || 'Failed to parse the uploaded file.');
                clearPreview();
            } finally {
                this.value = '';
            }
        });

        $('#submitPoUploadBtn').on('click', function () {
            if (!previewRows.length) {
                notifyPoUploadError('Please upload a file before submit.');
                return;
            }

            if (invalidRows.length) {
                notifyPoUploadError('Please correct the highlighted mandatory fields and re-upload the file.');
                return;
            }

            submitPoUploadData();
        });
    });

    function clearPreview() {
        previewRows = [];
        invalidRows = [];
        $('#poUploadSummary').addClass('d-none').empty();
        $('#poUploadErrors').addClass('d-none').empty();
        $('#poUploadPreviewState').html(`
            <div class="csr-po-upload-empty text-center">
                <p class="mb-2">No file uploaded yet.</p>
                <small class="text-muted">Download the sample format, fill the rows, then upload to preview the data.</small>
            </div>
        `);
    }

    function renderPreview(rows, fileName) {
        previewRows = rows;
        invalidRows = [];

        const normalizedRows = rows.map(function (row, index) {
            const validation = validateRow(row);
            if (validation.errors.length) {
                invalidRows.push({
                    rowNumber: index + 1,
                    errors: validation.errors
                });
            }
            return {
                rowNumber: index + 1,
                values: validation.values,
                invalidFields: validation.invalidFields
            };
        });

        renderSummary(fileName, normalizedRows.length, invalidRows.length);
        renderErrorList(invalidRows);
        renderPreviewTable(normalizedRows);
    }

    function renderSummary(fileName, totalRows, invalidCount) {
        const summaryHtml = `
            <div class="alert alert-secondary mb-0">
                <div><strong>File:</strong> ${escapeHtml(fileName)}</div>
                <div><strong>Total Rows:</strong> ${totalRows}</div>
                <div><strong>Invalid Rows:</strong> ${invalidCount}</div>
            </div>
        `;
        $('#poUploadSummary').removeClass('d-none').html(summaryHtml);
    }

    function renderErrorList(errors) {
        if (!errors.length) {
            $('#poUploadErrors')
                .removeClass('d-none')
                .html('<div class="alert alert-success mb-0">All mandatory fields are filled.</div>');
            return;
        }

        const items = errors.map(function (item) {
            return `<li>Row ${item.rowNumber}: ${escapeHtml(item.errors.join(', '))}</li>`;
        }).join('');

        $('#poUploadErrors')
            .removeClass('d-none')
            .html(`
                <div class="alert alert-danger mb-0">
                    <strong>Mandatory field errors found:</strong>
                    <ul class="mb-0 mt-2">${items}</ul>
                </div>
            `);
    }

    function renderPreviewTable(rows) {
        if (!rows.length) {
            $('#poUploadPreviewState').html('<div class="alert alert-warning mb-0">No preview rows available.</div>');
            return;
        }

        const headerHtml = COLUMN_ORDER.map(function (header) {
            const requiredMark = REQUIRED_HEADERS.includes(header) ? ' <span class="text-danger">*</span>' : '';
            return `<th>${escapeHtml(header)}${requiredMark}</th>`;
        }).join('');

        const bodyHtml = rows.map(function (row) {
            const cells = COLUMN_ORDER.map(function (header) {
                const value = row.values[header] || '';
                const isInvalid = row.invalidFields.includes(header);
                return `<td class="${isInvalid ? 'po-upload-invalid-cell' : ''}">${escapeHtml(value)}</td>`;
            }).join('');

            return `<tr><td>${row.rowNumber}</td>${cells}</tr>`;
        }).join('');

        $('#poUploadPreviewState').html(`
            <div class="table-responsive">
                <table class="table table-bordered table-striped po-upload-preview-table">
                    <thead>
                        <tr>
                            <th>Row</th>
                            ${headerHtml}
                        </tr>
                    </thead>
                    <tbody>${bodyHtml}</tbody>
                </table>
            </div>
        `);
    }

    function validateRow(row) {
        const values = {};
        const invalidFields = [];
        const errors = [];

        COLUMN_ORDER.forEach(function (header) {
            values[header] = normalizeCellValue(row[header] || '', header);
        });

        REQUIRED_HEADERS.forEach(function (header) {
            if (!values[header]) {
                invalidFields.push(header);
                errors.push(`${header} is required`);
            }
        });

        const poAvailable = (values['PO Available'] || '').toLowerCase();
        if (poAvailable === 'yes' || poAvailable === 'y' || poAvailable === '0' || poAvailable === 'true') {
            if (!values['PO No']) {
                invalidFields.push('PO No');
                errors.push('PO No is required when PO Available is Yes');
            }
        }

        return {
            values: values,
            invalidFields: invalidFields,
            errors: errors
        };
    }

    async function parseUploadFile(file, extension) {
        if (extension === 'csv') {
            const text = await file.text();
            return parseDelimitedText(text);
        }

        if (extension === 'xlsx') {
            return parseXlsxFile(file);
        }

        return parseXlsLikeFile(file);
    }

    async function parseXlsxFile(file) {
        if (typeof JSZip === 'undefined') {
            throw new Error('XLSX preview library is not available.');
        }

        const arrayBuffer = await file.arrayBuffer();
        const zip = await JSZip.loadAsync(arrayBuffer);
        const workbookXml = await readZipText(zip, 'xl/workbook.xml');
        const relsXml = await readZipText(zip, 'xl/_rels/workbook.xml.rels');
        const sharedStringsXml = await readZipText(zip, 'xl/sharedStrings.xml', true);

        const parser = new DOMParser();
        const workbookDoc = parser.parseFromString(workbookXml, 'application/xml');
        const relsDoc = parser.parseFromString(relsXml, 'application/xml');
        const sharedStrings = sharedStringsXml ? parseSharedStrings(parser.parseFromString(sharedStringsXml, 'application/xml')) : [];

        const firstSheet = workbookDoc.getElementsByTagName('sheet')[0];
        if (!firstSheet) {
            return [];
        }

        const relationId = firstSheet.getAttribute('r:id');
        let targetPath = '';
        const relations = relsDoc.getElementsByTagName('Relationship');
        for (let i = 0; i < relations.length; i++) {
            if (relations[i].getAttribute('Id') === relationId) {
                targetPath = relations[i].getAttribute('Target') || '';
                break;
            }
        }

        if (!targetPath) {
            throw new Error('Worksheet path not found in uploaded XLSX file.');
        }

        if (!targetPath.startsWith('xl/')) {
            targetPath = `xl/${targetPath.replace(/^\/+/, '')}`;
        }

        const sheetXml = await readZipText(zip, targetPath);
        const sheetDoc = parser.parseFromString(sheetXml, 'application/xml');
        return parseSheetRows(sheetDoc, sharedStrings);
    }

    async function parseXlsLikeFile(file) {
        const buffer = await file.arrayBuffer();
        const decoder = new TextDecoder('utf-8', { fatal: false });
        const text = decoder.decode(buffer);

        if (text.indexOf('<table') !== -1) {
            return parseHtmlTable(text);
        }

        if (text.indexOf('<Row') !== -1 || text.indexOf('<Worksheet') !== -1) {
            return parseSpreadsheetXml(text);
        }

        if (text.indexOf('\t') !== -1 || text.indexOf(',') !== -1) {
            return parseDelimitedText(text);
        }

        throw new Error('The uploaded .xls file could not be previewed. Please use .xlsx or .csv for this preview step.');
    }

    function parseSheetRows(sheetDoc, sharedStrings) {
        const rows = [];
        const rowNodes = sheetDoc.getElementsByTagName('row');

        for (let i = 0; i < rowNodes.length; i++) {
            const rowNode = rowNodes[i];
            const rowData = [];
            const cells = rowNode.getElementsByTagName('c');

            for (let j = 0; j < cells.length; j++) {
                const cell = cells[j];
                const cellRef = cell.getAttribute('r') || '';
                const colIndex = columnLettersToIndex(cellRef.replace(/[0-9]/g, ''));
                const type = cell.getAttribute('t') || '';
                let value = '';

                if (type === 'inlineStr') {
                    value = getNodeText(cell.getElementsByTagName('t')[0]);
                } else {
                    value = getNodeText(cell.getElementsByTagName('v')[0]);
                    if (type === 's') {
                        value = sharedStrings[parseInt(value, 10)] || '';
                    }
                }

                rowData[colIndex] = value;
            }

            if (rowData.some(function (item) { return String(item || '').trim() !== ''; })) {
                rows.push(rowData);
            }
        }

        return rows;
    }

    function parseSharedStrings(sharedStringsDoc) {
        const items = sharedStringsDoc.getElementsByTagName('si');
        const strings = [];

        for (let i = 0; i < items.length; i++) {
            const textNodes = items[i].getElementsByTagName('t');
            let value = '';
            for (let j = 0; j < textNodes.length; j++) {
                value += getNodeText(textNodes[j]);
            }
            strings.push(value);
        }

        return strings;
    }

    function parseSpreadsheetXml(xmlText) {
        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(xmlText, 'application/xml');
        const rowNodes = xmlDoc.getElementsByTagName('Row');
        const rows = [];

        for (let i = 0; i < rowNodes.length; i++) {
            const cells = rowNodes[i].getElementsByTagName('Cell');
            const row = [];
            for (let j = 0; j < cells.length; j++) {
                const dataNode = cells[j].getElementsByTagName('Data')[0];
                row.push(getNodeText(dataNode));
            }
            rows.push(row);
        }

        return rows;
    }

    function parseHtmlTable(htmlText) {
        const parser = new DOMParser();
        const htmlDoc = parser.parseFromString(htmlText, 'text/html');
        const table = htmlDoc.querySelector('table');
        if (!table) {
            return [];
        }

        return Array.from(table.querySelectorAll('tr')).map(function (row) {
            return Array.from(row.querySelectorAll('th, td')).map(function (cell) {
                return (cell.textContent || '').trim();
            });
        }).filter(function (row) {
            return row.some(function (cell) { return cell !== ''; });
        });
    }

    function parseDelimitedText(text) {
        const sanitized = text.replace(/\r\n/g, '\n').replace(/\r/g, '\n').trim();
        if (!sanitized) {
            return [];
        }

        const delimiter = detectDelimiter(sanitized);
        const rows = [];
        let current = '';
        let row = [];
        let inQuotes = false;

        for (let i = 0; i < sanitized.length; i++) {
            const char = sanitized[i];
            const nextChar = sanitized[i + 1];

            if (char === '"') {
                if (inQuotes && nextChar === '"') {
                    current += '"';
                    i++;
                } else {
                    inQuotes = !inQuotes;
                }
                continue;
            }

            if (char === delimiter && !inQuotes) {
                row.push(current.trim());
                current = '';
                continue;
            }

            if (char === '\n' && !inQuotes) {
                row.push(current.trim());
                rows.push(row);
                row = [];
                current = '';
                continue;
            }

            current += char;
        }

        row.push(current.trim());
        rows.push(row);
        return rows.filter(function (item) {
            return item.some(function (cell) { return cell !== ''; });
        });
    }

    function mapRowsFromUpload(rows) {
        if (!rows.length) {
            return [];
        }

        const headerRow = rows[0].map(function (header) {
            return normalizeHeader(header);
        });

        return rows.slice(1).map(function (row) {
            const mapped = {};
            COLUMN_ORDER.forEach(function (header, index) {
                const headerIndex = headerRow.indexOf(normalizeHeader(header));
                mapped[header] = headerIndex > -1 ? String(row[headerIndex] || '').trim() : '';
            });
            return mapped;
        }).filter(function (row) {
            return Object.keys(row).some(function (key) {
                return row[key] !== '';
            });
        });
    }

    function normalizeCellValue(value, header) {
        const trimmed = String(value || '').trim();
        if (!trimmed) {
            return '';
        }

        if (header === 'PO Date' && /^\d+(\.\d+)?$/.test(trimmed)) {
            return excelSerialToDate(trimmed);
        }

        return trimmed;
    }

    function excelSerialToDate(serialValue) {
        const serial = parseFloat(serialValue);
        if (isNaN(serial)) {
            return serialValue;
        }

        const utcDays = Math.floor(serial - 25569);
        const utcValue = utcDays * 86400;
        const dateInfo = new Date(utcValue * 1000);

        if (isNaN(dateInfo.getTime())) {
            return serialValue;
        }

        const year = dateInfo.getUTCFullYear();
        const month = String(dateInfo.getUTCMonth() + 1).padStart(2, '0');
        const day = String(dateInfo.getUTCDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function detectDelimiter(text) {
        const firstLine = text.split('\n')[0] || '';
        const commaCount = (firstLine.match(/,/g) || []).length;
        const tabCount = (firstLine.match(/\t/g) || []).length;
        return tabCount > commaCount ? '\t' : ',';
    }

    function normalizeHeader(header) {
        return String(header || '')
            .trim()
            .toLowerCase()
            .replace(/\s+/g, ' ')
            .replace(/\.+/g, '')
            .replace(/\(\s*/g, '(')
            .replace(/\s*\)/g, ')');
    }

    function columnLettersToIndex(letters) {
        let index = 0;
        for (let i = 0; i < letters.length; i++) {
            index = index * 26 + (letters.charCodeAt(i) - 64);
        }
        return index - 1;
    }

    function getNodeText(node) {
        return node && node.textContent ? node.textContent : '';
    }

    async function readZipText(zip, path, optional) {
        const file = zip.file(path);
        if (!file) {
            if (optional) {
                return '';
            }
            throw new Error(`Missing worksheet data: ${path}`);
        }
        return file.async('string');
    }

    function parseJsonData(value, fallback) {
        try {
            return JSON.parse(value || '[]');
        } catch (error) {
            return fallback;
        }
    }

    function downloadSampleCsv(headers) {
        const sampleRow = [
            'Yes',
            '2025-26',
            'PO-001',
            '2026-04-29',
            '10000',
            '10000',
            '18',
            '11800',
            'BP001',
            'Sample Business',
            'Sample PO details',
            'Sample remarks'
        ];

        const csvContent = [
            headers.join(','),
            sampleRow.join(',')
        ].join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'csr_po_upload_sample.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(link.href);
    }

    function getFileExtension(fileName) {
        return String(fileName || '').split('.').pop().toLowerCase();
    }

    function submitPoUploadData() {
        const cfg = document.getElementById('poUploadConfig')?.dataset || {};
        const formData = new FormData();

        formData.append('page', 'save_po_excel_upload');
        formData.append('tracking_id', cfg.trackingId || '');
        formData.append('project_id', cfg.projectId || '');
        formData.append('doc_type', cfg.docType || 'PO');

        previewRows.forEach(function (row) {
            formData.append('po_available[]', row['PO Available'] || '');
            formData.append('financial_year[]', row['Financial Year'] || '');
            formData.append('po_no[]', row['PO No'] || '');
            formData.append('po_date[]', row['PO Date'] || '');
            formData.append('po_amount[]', row['PO Amount'] || '');
            formData.append('po_value_ex[]', row['PO Value (Excl. Tax)'] || '');
            formData.append('gst[]', row['GST %'] || '');
            formData.append('po_value_in[]', row['PO Value (Incl. Tax)'] || '');
            formData.append('business_code[]', row['Business Code'] || '');
            formData.append('business_name[]', row['Business Name'] || '');
            formData.append('po_details[]', row['Other PO Details'] || '');
            formData.append('remarks[]', row['Remarks'] || '');
        });

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                let data = response;

                if (typeof response == 'string') {
                    try {
                        data = JSON.parse(response);
                    } catch (error) {
                        notifyPoUploadError('Unexpected response received while saving uploaded PO data.');
                        return;
                    }
                }

                if (data && data.status == 'success') {
                    tostarnotify(
                        'top', 'right', 'fa fa-thumbs-up',
                        'form-label label label-success',
                        'animated flipInX', 'animated flipOutX',
                        data.message || 'PO upload saved successfully.', ''
                    );
                    setTimeout(function () {
                        if (typeof openCsrPoInvoiceResult === 'function') {
                            openCsrPoInvoiceResult({
                                project_id: cfg.projectId || '',
                                tracking_id: cfg.trackingId || '',
                                doc_type: cfg.docType || 'PO'
                            });
                        }
                    }, 1500);
                } else if (data && data.status == 'partial') {
                    notifyPoUploadError(data.message || 'Some uploaded PO rows could not be saved.');
                } else {
                    notifyPoUploadError((data && data.message) ? data.message : 'Failed to save uploaded PO data.');
                }
            },
            error: function () {
                notifyPoUploadError('An error occurred while saving uploaded PO data.');
            }
        });
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function notifyPoUploadError(message) {
        tostarnotify(
            'top', 'right', 'fa fa-thumbs-down',
            'form-label label label-danger',
            'animated flipInX', 'animated flipOutX',
            message, ''
        );
    }
})(jQuery);
