function generateTableRows() {
    const tableBody = document.getElementById("tableBody");
    tableBody.innerHTML = ""; // Clear previous table rows

    db_paieska_table.forEach(item => {
        const row = document.createElement("tr");
        const nameCell = document.createElement("td");
        const perziurosCell = document.createElement("td");
        const tooltipContainer = document.createElement("div");
        const tooltipText = document.createElement("span");

        nameCell.textContent = item.pavadinimas;
        perziurosCell.textContent = item.perziuros;

        // Add the tooltip class to the tooltip container
        tooltipContainer.classList.add("tooltip");

        // Add the tooltiptext class to the tooltip text
        tooltipText.classList.add("tooltiptext");

        // Set the tooltip text
        tooltipText.textContent = item.organizacija + '\n' +'Atnaujinimo data: ' + item.data;


        // Append the tooltip text to the tooltip container
        tooltipContainer.appendChild(tooltipText);

        // Append the tooltip container to the name cell
        nameCell.appendChild(tooltipContainer);

        row.appendChild(nameCell);
        row.appendChild(perziurosCell);

        tableBody.appendChild(row);

        // Add event listener to show tooltip on hover
        
        row.addEventListener("click", function() {
            window.open(item.nuoroda, "_blank"); // Open the link in a new tab
        });
    });
}

function filterTable() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchBar");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }       
    }
}
