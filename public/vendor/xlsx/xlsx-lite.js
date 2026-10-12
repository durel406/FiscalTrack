(function () {
  function escapeCsv(value) {
    var raw = value == null ? '' : String(value);
    return /[",\n]/.test(raw) ? '"' + raw.replace(/"/g, '""') + '"' : raw;
  }

  function aoa_to_sheet(data) {
    return {
      __rows: (data || []).map(function (row) {
        return (row || []).map(function (cell) {
          return cell == null ? '' : cell;
        });
      })
    };
  }

  function book_new() {
    return { Sheets: {}, SheetNames: [] };
  }

  function book_append_sheet(wb, ws, name) {
    wb.SheetNames.push(name);
    wb.Sheets[name] = ws;
  }

  function writeFile(wb, filename) {
    var name = String(filename || 'export.csv');
    var sheetName = wb.SheetNames && wb.SheetNames[0] ? wb.SheetNames[0] : 'Sheet1';
    var ws = wb.Sheets && wb.Sheets[sheetName] ? wb.Sheets[sheetName] : { __rows: [] };
    var rows = ws.__rows || [];
    var csv = rows.map(function (row) {
      return (row || []).map(function (cell) {
        return escapeCsv(cell);
      }).join(',');
    }).join('\n');

    var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    var url = URL.createObjectURL(blob);
    var link = document.createElement('a');
    link.href = url;
    link.download = name.replace(/\.xlsx?$/i, '.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  }

  window.XLSX = {
    utils: {
      aoa_to_sheet: aoa_to_sheet,
      book_new: book_new,
      book_append_sheet: book_append_sheet,
    },
    writeFile: writeFile
  };
})();
