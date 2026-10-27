(function ($) {
    const REQUIRED_HEADERS = [
        'Financial Year',
        'Invoice No',
        'Invoice Date',
        'Taxable Value',
        'GST Amount',
        'Total Spent',
        'Net Payable',
        'Paid Date',
        'Paid Amount',
        'Paid Bank'
    ];

    const COLUMN_ORDER = [
        'PO No',
        'Financial Year',
        'Invoice No',
        'Invoice Date',
        'Taxable Value',
        'GST Amount',
        'Total Spent',
        'TDS Deducted',
        'Net Payable',
        'Paid Date',
        'Paid Amount',
        'Paid Bank'
    ];

    let previewRows = [];
    let invalidRows = [];

    $(document).ready(function () {
        const cfgNode = document.getElementById('invoiceUploadConfig');
        if (!cfgNode) {
            return;
        }

        const cfg = cfgNode.dataset || {};
        const sampleHeaders = parseJsonData(cfg.sampleHeaders, COLUMN_ORDER);

        $('#downloadInvoiceSampleBtn').on('click', function () {
            downloadSampleCsv(sampleHeaders);
        });

        $('#triggerInvoiceUploadBtn').on('click', function () {
            $('#invoiceUploadInput').trigger('click');
        });

        $('#invoiceUploadInput').on('change', async function () {
            const file = this.files[0];
            if (!file) {
                return;
            }

            const extension = getFileExtension(file.name);
            if (!['csv', 'xlsx', 'xls'].includes(extension)) {
                notifyInvoiceUploadError('Only .xlsx, .xls, and .csv files are allowed.');
                this.value = '';
                return;
            }

            try {
                const rows = await parseUploadFile(file, extension);
                if (!rows.length) {
                    notifyInvoiceUploadError('No rows found in the uploaded file.');
                    clearPreview();
                    return;
                }

                const mappedRows = mapRowsFromUpload(rows);
                if (!mappedRows.length) {
                    notifyInvoiceUploadError('The uploaded file does not contain usable row data.');
                    clearPreview();
                    return;
                }

                renderPreview(mappedRows, file.name);
            } catch (error) {
                console.error(error);
                notifyInvoiceUploadError(error.message || 'Failed to parse the uploaded file.');
                clearPreview();
            } finally {
                this.value = '';
            }
        });

        $('#submitInvoiceUploadBtn').on('click', function () {
            if (!previewRows.length) {
                notifyInvoiceUploadError('Please upload a file before submit.');
                return;
            }

            if (invalidRows.length) {
                notifyInvoiceUploadError('Please correct the highlighted mandatory fields and re-upload the file.');
                return;
            }

            submitInvoiceUploadData();
        });
    });

    function clearPreview() {
        previewRows = [];
        invalidRows = [];
        $('#invoiceUploadSummary').addClass('d-none').empty();
        $('#invoiceUploadErrors').addClass('d-none').empty();
        $('#invoiceUploadPreviewState').html(`
            <div class="csr-invoice-upload-empty text-center">
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
        $('#invoiceUploadSummary').removeClass('d-none').html(`
            <div class="alert alert-secondary mb-0">
                <div><strong>File:</strong> ${escapeHtml(fileName)}</div>
                <div><strong>Total Rows:</strong> ${totalRows}</div>
                <div><strong>Invalid Rows:</strong> ${invalidCount}</div>
            </div>
        `);
    }

    function renderErrorList(errors) {
        if (!errors.length) {
            $('#invoiceUploadErrors')
                .removeClass('d-none')
                .html('<div class="alert alert-success mb-0">All mandatory fields are filled.</div>');
            return;
        }

        const items = errors.map(function (item) {
            return `<li>Row ${item.rowNumber}: ${escapeHtml(item.errors.join(', '))}</li>`;
        }).join('');

        $('#invoiceUploadErrors')
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
            $('#invoiceUploadPreviewState').html('<div class="alert alert-warning mb-0">No preview rows available.</div>');
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
                return `<td class="${isInvalid ? 'invoice-upload-invalid-cell' : ''}">${escapeHtml(value)}</td>`;
            }).join('');

            return `<tr><td>${row.rowNumber}</td>${cells}</tr>`;
        }).join('');

        $('#invoiceUploadPreviewState').html(`
            <div class="table-responsive">
                <table class="table table-bordered table-striped invoice-upload-preview-table">
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
            COLUMN_ORDER.forEach(function (header) {
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

        if ((header === 'Invoice Date' || header === 'Paid Date') && /^\d+(\.\d+)?$/.test(trimmed)) {
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
            'PO-001',
            '2025-26',
            'INV-001',
            '2026-04-29',
            '10000',
            '1800',
            '11800',
            '500',
            '11300',
            '2026-04-30',
            '11300',
            'Sample Bank'
        ];

        const csvContent = [
            headers.join(','),
            sampleRow.join(',')
        ].join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'csr_invoice_upload_sample.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(link.href);
    }

    function getFileExtension(fileName) {
        return String(fileName || '').split('.').pop().toLowerCase();
    }

    function submitInvoiceUploadData() {
        const cfg = document.getElementById('invoiceUploadConfig')?.dataset || {};
        const formData = new FormData();

        formData.append('page', 'save_invoice_excel_upload');
        formData.append('tracking_id', cfg.trackingId || '');
        formData.append('project_id', cfg.projectId || '');
        formData.append('doc_type', cfg.docType || 'INVOICE');

        previewRows.forEach(function (row) {
            formData.append('po_no[]', row['PO No'] || '');
            formData.append('financial_year[]', row['Financial Year'] || '');
            formData.append('inv_no[]', row['Invoice No'] || '');
            formData.append('inv_date[]', row['Invoice Date'] || '');
            formData.append('taxable_value[]', row['Taxable Value'] || '');
            formData.append('gst_amount[]', row['GST Amount'] || '');
            formData.append('total_spent[]', row['Total Spent'] || '');
            formData.append('tds_deducted[]', row['TDS Deducted'] || '');
            formData.append('net_payable[]', row['Net Payable'] || '');
            formData.append('paid_date[]', row['Paid Date'] || '');
            formData.append('paid_amount[]', row['Paid Amount'] || '');
            formData.append('paid_bank[]', row['Paid Bank'] || '');
        });

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                let data = response;

                if (typeof response === 'string') {
                    try {
                        data = JSON.parse(response);
                    } catch (error) {
                        notifyInvoiceUploadError('Unexpected response received while saving uploaded invoice data.');
                        return;
                    }
                }

                if (data && data.status === 'success') {
                    tostarnotify(
                        'top', 'right', 'fa fa-thumbs-up',
                        'form-label label label-success',
                        'animated flipInX', 'animated flipOutX',
                        (data.message || 'Invoice upload saved successfully.') + formatMissingPoMessage(data.missing_po_rows || []), ''
                    );
                    setTimeout(function () {
                        if (typeof openCsrPoInvoiceResult === 'function') {
                            openCsrPoInvoiceResult({
                                project_id: cfg.projectId || '',
                                tracking_id: cfg.trackingId || '',
                                doc_type: cfg.docType || 'INVOICE'
                            });
                        }
                    }, 1500);
                } else if (data && data.status === 'partial') {
                    notifyInvoiceUploadError((data.message || 'Some uploaded invoice rows could not be saved.') + formatMissingPoMessage(data.missing_po_rows || []));
                } else {
                    notifyInvoiceUploadError((data && data.message) ? data.message : 'Failed to save uploaded invoice data.');
                }
            },
            error: function () {
                notifyInvoiceUploadError('An error occurred while saving uploaded invoice data.');
            }
        });
    }

    function formatMissingPoMessage(rows) {
        if (!rows.length) {
            return '';
        }

        const details = rows.map(function (item) {
            return `Row ${item.row} (${item.po_no || 'blank PO'})`;
        }).join(', ');

        return ` Missing PO mapping: ${details}.`;
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function notifyInvoiceUploadError(message) {
        tostarnotify(
            'top', 'right', 'fa fa-thumbs-down',
            'form-label label label-danger',
            'animated flipInX', 'animated flipOutX',
            message, ''
        );
    }
})(jQuery);
