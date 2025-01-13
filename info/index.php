<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dynamic Table with Filters, Pagination, and Sorting</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
  <style>
    .table-container {
      margin-top: 20px;
    }
    .search-container {
      margin-bottom: 20px;
    }
    .pagination-container {
      margin-top: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .pagination-container select {
      width: auto;
    }
    .pagination-button {
      cursor: pointer;
    }
    .pagination .page-item.active .page-link {
      background-color: #007bff;
      border-color: #007bff;
    }
    .sort-button {
      cursor: pointer;
    }
    th {
      position: relative;
    }
    .sort-buttons {
      position: absolute;
      right: 10px;
      top: 5px;
      font-size: 18px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row search-container">
      <div class="col-md-6">
        <input type="text" id="searchInput" class="form-control" placeholder="Search table">
      </div>
      <div class="col-md-6 text-right">
        <button class="btn btn-primary">Add Row</button>
        <button class="btn btn-secondary">Export</button>
      </div>
    </div>

    <!-- Table -->
    <div class="table-container">
      <table id="table1" class="table table-bordered">
        <thead>
          <tr>
            <th id="col1Header">Name 
              <div class="sort-buttons">
                <span class="sort-button" id="sortNameAsc"> &lt; </span>
                <span class="sort-button" id="sortNameDesc"> &gt; </span>
              </div>
            </th>
            <th id="col2Header">Age 
              <div class="sort-buttons">
                <span class="sort-button" id="sortAgeAsc"> &lt; </span>
                <span class="sort-button" id="sortAgeDesc"> &gt; </span>
              </div>
            </th>
            <th id="col3Header">Country 
              <div class="sort-buttons">
                <span class="sort-button" id="sortCountryAsc"> &lt; </span>
                <span class="sort-button" id="sortCountryDesc"> &gt; </span>
              </div>
            </th>
          </tr>
        </thead>
        <tbody id="table1Body">
          <!-- Table rows will be inserted dynamically -->
        </tbody>
      </table>
    </div>

    <!-- Pagination Controls -->
    <div class="pagination-container">
      <div>
        <label for="rowsPerPage">Rows per page:</label>
        <select id="rowsPerPage" class="form-control form-control-sm">
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
          <option value="250">250</option>
          <option value="1000">1000</option>
        </select>
      </div>
      <nav>
        <ul class="pagination" id="pagination">
          <!-- Pagination buttons will be dynamically rendered here -->
        </ul>
      </nav>
    </div>
  </div>

  <script>
    let data = [];
    for (let i = 1; i <= 128; i++) {
      data.push({
        name: `Name ${i}`,
        age: Math.floor(Math.random() * 100) + 18,
        country: `Country ${Math.floor(Math.random() * 20) + 1}`
      });
    }

    const rowsPerPageOptions = [10, 25, 50, 100, 250, 1000];
    let rowsPerPage = 10;
    let currentPage = 1;
    let sortOrder = {
      name: 'asc',
      age: 'asc',
      country: 'asc'
    };

    // Function to render the table based on selector
    function renderTable(selector, dataToRender) {
      const tableBody = document.querySelector(`${selector} tbody`);
      tableBody.innerHTML = ""; // Clear existing rows

      dataToRender.forEach(item => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${item.name}</td>
          <td>${item.age}</td>
          <td>${item.country}</td>
        `;
        tableBody.appendChild(row);
      });
    }

    // Function to render pagination
    function renderPagination(totalRecords) {
      const pagination = document.getElementById("pagination");
      pagination.innerHTML = ""; // Clear existing pagination buttons

      const totalPages = Math.ceil(totalRecords / rowsPerPage);
      const prevPage = document.getElementById("prevPage");
      const nextPage = document.getElementById("nextPage");

      // Create Previous button
      const prevButton = document.createElement("li");
      prevButton.classList.add("page-item");
      prevButton.innerHTML = `<a class="page-link" href="#">Previous</a>`;
      prevButton.addEventListener("click", function() {
        if (currentPage > 1) {
          currentPage--;
          updateTable();
        }
      });
      pagination.appendChild(prevButton);

      // Create page number buttons
      const pageButtonRange = 5;
      let startPage = Math.max(1, currentPage - Math.floor(pageButtonRange / 2));
      let endPage = Math.min(totalPages, startPage + pageButtonRange - 1);

      if (endPage - startPage < pageButtonRange - 1) {
        startPage = Math.max(1, endPage - pageButtonRange + 1);
      }

      for (let i = startPage; i <= endPage; i++) {
        const pageItem = document.createElement("li");
        pageItem.classList.add("page-item");
        if (i === currentPage) {
          pageItem.classList.add("active");
        }
        pageItem.innerHTML = `<a class="page-link" href="#">${i}</a>`;
        pageItem.addEventListener("click", function() {
          currentPage = i;
          updateTable();
        });
        pagination.appendChild(pageItem);
      }

      // Create Next button
      const nextButton = document.createElement("li");
      nextButton.classList.add("page-item");
      nextButton.innerHTML = `<a class="page-link" href="#">Next</a>`;
      nextButton.addEventListener("click", function() {
        if (currentPage < totalPages) {
          currentPage++;
          updateTable();
        }
      });
      pagination.appendChild(nextButton);
    }

    // Function to update the table based on current page and rows per page
    function updateTable() {
      const startIndex = (currentPage - 1) * rowsPerPage;
      const endIndex = currentPage * rowsPerPage;
      const dataToDisplay = data.slice(startIndex, endIndex);
      renderTable("#table1", dataToDisplay);
      renderPagination(data.length);
    }

    // Handle search functionality
    document.getElementById("searchInput").addEventListener("input", function() {
      const searchTerm = this.value.toLowerCase();
      const filteredData = data.filter(item =>
        item.name.toLowerCase().includes(searchTerm) ||
        item.age.toString().includes(searchTerm) ||
        item.country.toLowerCase().includes(searchTerm)
      );
      renderTable("#table1", filteredData.slice(0, rowsPerPage));
      renderPagination(filteredData.length);
    });

    // Handle row length change
    document.getElementById("rowsPerPage").addEventListener("change", function() {
      rowsPerPage = parseInt(this.value, 10);
      updateTable();
    });

    // Handle column sorting
    function sortData(column, order) {
      const sortedData = [...data].sort((a, b) => {
        if (order === 'asc') {
          return a[column] > b[column] ? 1 : -1;
        } else {
          return a[column] < b[column] ? 1 : -1;
        }
      });
      data = sortedData;
      updateTable();
    }

    // Sort buttons for columns
    document.getElementById("sortNameAsc").addEventListener("click", function() {
      sortOrder.name = 'asc';
      sortData('name', 'asc');
    });
    document.getElementById("sortNameDesc").addEventListener("click", function() {
      sortOrder.name = 'desc';
      sortData('name', 'desc');
    });

    document.getElementById("sortAgeAsc").addEventListener("click", function() {
      sortOrder.age = 'asc';
      sortData('age', 'asc');
    });
    document.getElementById("sortAgeDesc").addEventListener("click", function() {
      sortOrder.age = 'desc';
      sortData('age', 'desc');
    });

    document.getElementById("sortCountryAsc").addEventListener("click", function() {
      sortOrder.country = 'asc';
      sortData('country', 'asc');
    });
    document.getElementById("sortCountryDesc").addEventListener("click", function() {
      sortOrder.country = 'desc';
      sortData('country', 'desc');
    });

    // Initial render
    updateTable();
  </script>
</body>
</html>
