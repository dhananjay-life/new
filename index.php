<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Table</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">

    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">🚀 Dynamic Table with Auto Save</h2>

        <table class="w-full border-collapse border border-gray-300 rounded-lg" id="dataTable">
            <thead class="bg-blue-500 text-white">
                <tr>
                    <th class="p-2 border">Date</th>
                    <th class="p-2 border">Sr Name</th>
                    <th class="p-2 border">Sr ID</th>
                    <th class="p-2 border">OFD</th>
                    <th class="p-2 border">Delivery</th>
                    <th class="p-2 border">Rate</th>
                    <th class="p-2 border">Fuel</th>
                    <th class="p-2 border">Advance</th>
                    <th class="p-2 border">Payment</th>
                    <th class="p-2 border">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="bg-gray-100 font-semibold">
                <tr>
                    <td colspan="8" class="text-right p-2 border">Total Payment:</td>
                    <td class="p-2 border" id="totalPayment">0</td>
                    <td class="border"></td>
                </tr>
            </tfoot>
        </table>

        <div class="mt-4 flex justify-between">
            <button onclick="addRow()" class="bg-blue-500 text-white px-4 py-2 rounded">➕ Add Row</button>
            <button onclick="clearData()" class="bg-red-500 text-white px-4 py-2 rounded">🗑️ Clear</button>
            <button onclick="downloadCSV()" class="bg-green-500 text-white px-4 py-2 rounded">⬇️ Download CSV</button>
            <button onclick="sendToServer()" class="bg-purple-500 text-white px-4 py-2 rounded">💾 Save to Server</button>

        </div>
    </div>
    <div class="max-w-6xl mx-auto mt-10">
    <h2 class="text-xl font-semibold mb-4">📊 Filter Delivery Data (SR + Date)</h2>

    <div class="flex flex-wrap gap-4 items-center mb-4">
        <select id="srSelect" class="p-2 border rounded w-60">
            <option value="">-- All SRs --</option>
        </select>
        <input type="date" id="fromDate" class="p-2 border rounded" />
        <input type="date" id="toDate" class="p-2 border rounded" />
        <button onclick="applyFilters()" class="bg-blue-600 text-white px-4 py-2 rounded">🔍 Filter</button>
    </div>

    <table class="w-full border border-gray-300 text-sm" id="srDataTable">
        <thead class="bg-blue-100">
            <tr>
                <th class="border p-2">Date</th>
                <th class="border p-2">SR Name</th>
                <th class="border p-2">SR ID</th>
                <th class="border p-2">OFD</th>
                <th class="border p-2">Delivery</th>
                <th class="border p-2">Rate</th>
                <th class="border p-2">Fuel</th>
                <th class="border p-2">Advance</th>
                <th class="border p-2">Payment</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>


    <script>
        function escapeCSV(val) {
            return `"${String(val).replace(/"/g, '""')}"`;
        }

        function updateTotalPayment() {
            let total = 0;
            document.querySelectorAll(".payment").forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById("totalPayment").innerText = total.toFixed(2);
        }

        function updatePayment(input) {
            let row = input.closest("tr");
            let delivery = parseFloat(row.querySelector(".delivery").value) || 0;
            let rate = parseFloat(row.querySelector(".rate").value) || 0;
            let fuel = parseFloat(row.querySelector(".fuel").value) || 0;
            let advance = parseFloat(row.querySelector(".advance").value) || 0;
            let payment = row.querySelector(".payment");

            payment.value = (delivery * rate) - (fuel + advance);
            updateTotalPayment();
            saveData();
        }

        function addRow(data = {}) {
            let table = document.querySelector("#dataTable tbody");
            let row = document.createElement("tr");
            row.classList.add("bg-gray-50");

            row.innerHTML = `
        <td class="border p-2"><input type="date" class="date w-full p-1 border rounded" value="${data.date || new Date().toISOString().split('T')[0]}" oninput="saveData()"></td>
        <td class="border p-2"><input type="text" class="name w-full p-1 border rounded" value="${data.name || ''}" oninput="saveData()"></td>
        <td class="border p-2"><input type="number" class="srid w-full p-1 border rounded" value="${data.srid || ''}" min="0" oninput="saveData()"></td>
        <td class="border p-2"><input type="number" class="ofd w-full p-1 border rounded" value="${data.ofd || ''}" min="0" oninput="saveData()"></td>
        <td class="border p-2"><input type="number" class="delivery w-full p-1 border rounded" value="${data.delivery || ''}" min="0" step="any" oninput="updatePayment(this)"></td>
        <td class="border p-2"><input type="number" class="rate w-full p-1 border rounded" value="${data.rate || ''}" min="0" step="any" oninput="updatePayment(this)"></td>
        <td class="border p-2"><input type="number" class="fuel w-full p-1 border rounded" value="${data.fuel || ''}" min="0" step="any" oninput="updatePayment(this)"></td>
        <td class="border p-2"><input type="number" class="advance w-full p-1 border rounded" value="${data.advance || ''}" min="0" step="any" oninput="updatePayment(this)"></td>
        <td class="border p-2"><input type="number" class="payment w-full p-1 border rounded bg-gray-200" readonly value="${data.payment || ''}"></td>
        <td class="border p-2 text-center"><button class="bg-red-500 text-white px-3 py-1 rounded" onclick="removeRow(this)">❌</button></td>
        `;

            table.appendChild(row);
            row.querySelector("input").focus();
            updateTotalPayment();
        }

        function removeRow(button) {
            button.closest("tr").remove();
            saveData();
            updateTotalPayment();
        }

        function saveData() {
            let data = [];
            document.querySelectorAll("#dataTable tbody tr").forEach(row => {
                data.push({
                    date: row.querySelector(".date").value,
                    name: row.querySelector(".name").value,
                    srid: row.querySelector(".srid").value,
                    ofd: row.querySelector(".ofd").value,
                    delivery: row.querySelector(".delivery").value,
                    rate: row.querySelector(".rate").value,
                    fuel: row.querySelector(".fuel").value,
                    advance: row.querySelector(".advance").value,
                    payment: row.querySelector(".payment").value
                });
            });

            localStorage.setItem("tableData", JSON.stringify(data));
        }

        function loadData() {
            let savedData = JSON.parse(localStorage.getItem("tableData") || "[]");
            if (savedData.length === 0) addRow();
            else savedData.forEach(addRow);
        }

        function clearData() {
            if (confirm("Are you sure you want to clear all data?")) {
                localStorage.removeItem("tableData");
                document.querySelector("#dataTable tbody").innerHTML = "";
                addRow();
                updateTotalPayment();
                alert("🗑️ Data cleared.");
            }
        }

        function downloadCSV() {
            let rows = document.querySelectorAll("#dataTable tbody tr");
            let csvContent = "Date,Sr Name,Sr ID,OFD,Delivery,Rate,Fuel,Advance,Payment\n";

            rows.forEach(row => {
                let rowData = [
                    escapeCSV(row.querySelector(".date").value),
                    escapeCSV(row.querySelector(".name").value),
                    escapeCSV(row.querySelector(".srid").value),
                    escapeCSV(row.querySelector(".ofd").value),
                    escapeCSV(row.querySelector(".delivery").value),
                    escapeCSV(row.querySelector(".rate").value),
                    escapeCSV(row.querySelector(".fuel").value),
                    escapeCSV(row.querySelector(".advance").value),
                    escapeCSV(row.querySelector(".payment").value)
                ];
                csvContent += rowData.join(",") + "\n";
            });

            let blob = new Blob([csvContent], {
                type: "text/csv"
            });
            let a = document.createElement("a");
            a.href = URL.createObjectURL(blob);
            a.download = "table_data.csv";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            alert("✅ CSV downloaded successfully!");
        }

        window.onload = loadData;

        function sendToServer() {
            let data = [];
            document.querySelectorAll("#dataTable tbody tr").forEach(row => {
                data.push({
                    date: row.querySelector(".date").value,
                    name: row.querySelector(".name").value,
                    srid: row.querySelector(".srid").value,
                    ofd: row.querySelector(".ofd").value,
                    delivery: row.querySelector(".delivery").value,
                    rate: row.querySelector(".rate").value,
                    fuel: row.querySelector(".fuel").value,
                    advance: row.querySelector(".advance").value,
                    payment: row.querySelector(".payment").value
                });
            });

            fetch("save_data.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(response => {
                    alert(response.message);
                })
                .catch(error => {
                    console.error("Error saving data:", error);
                    alert("❌ Failed to save data.");
                });
        }

        function fetchSrData(srid) {
            fetch(`get_sr_data.php?srid=${srid}`)
                .then(res => res.json())
                .then(data => {
                    console.log("SR data:", data);
                    // You can now render it in a table or modal
                })
                .catch(err => console.error("Failed to fetch SR data", err));
        }

        function loadDataFromServer(srid = null) {
            let url = srid ? `get_data.php?srid=${srid}` : "get_data.php";

            fetch(url)
                .then(res => res.json())
                .then(res => {
                    if (res.status === "success") {
                        console.table(res.data); // You can display in table
                        // You can also render into the DOM here
                    } else {
                        alert("Error loading data: " + res.message);
                    }
                })
                .catch(err => {
                    console.error("Fetch error", err);
                });
        }
        document.addEventListener("DOMContentLoaded", function() {
            loadSrList(); // Load SR dropdown
            loadDataFromServer(); // Load all data initially
        });

        // Fetch SRs for dropdown
        function loadSrList() {
            fetch('get_sr_list.php') // You'll create this PHP file
                .then(res => res.json())
                .then(data => {
                    let srSelect = document.getElementById("srSelect");
                    data.forEach(sr => {
                        let option = document.createElement("option");
                        option.value = sr.srid;
                        option.text = `${sr.name} (ID: ${sr.srid})`;
                        srSelect.appendChild(option);
                    });
                });
        }

        // Fetch and render delivery data
        function loadDataFromServer(srid = "") {
            let url = srid ? `get_data.php?srid=${srid}` : "get_data.php";

            fetch(url)
                .then(res => res.json())
                .then(res => {
                    let tbody = document.querySelector("#srDataTable tbody");
                    tbody.innerHTML = "";

                    if (res.status === "success" && res.data.length) {
                        res.data.forEach(row => {
                            let tr = document.createElement("tr");
                            tr.innerHTML = `
                        <td class="border p-2">${row.date}</td>
                        <td class="border p-2">${row.name}</td>
                        <td class="border p-2">${row.srid}</td>
                        <td class="border p-2">${row.ofd}</td>
                        <td class="border p-2">${row.delivery}</td>
                        <td class="border p-2">${row.rate}</td>
                        <td class="border p-2">${row.fuel}</td>
                        <td class="border p-2">${row.advance}</td>
                        <td class="border p-2">${row.payment}</td>
                    `;
                            tbody.appendChild(tr);
                        });
                    } else {
                        tbody.innerHTML = `<tr><td colspan="9" class="text-center p-4 text-gray-500">No data found</td></tr>`;
                    }
                });
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            loadSrList();
            applyFilters(); // initial load
        });

        function loadSrList() {
            fetch('get_sr_list.php')
                .then(res => res.json())
                .then(data => {
                    const srSelect = document.getElementById("srSelect");
                    data.forEach(sr => {
                        const option = document.createElement("option");
                        option.value = sr.srid;
                        option.text = `${sr.name} (ID: ${sr.srid})`;
                        srSelect.appendChild(option);
                    });
                });
        }

        function applyFilters() {
            const srid = document.getElementById("srSelect").value;
            const from = document.getElementById("fromDate").value;
            const to = document.getElementById("toDate").value;

            let url = `get_data.php?`;
            if (srid) url += `srid=${srid}&`;
            if (from) url += `from=${from}&`;
            if (to) url += `to=${to}`;

            fetch(url)
                .then(res => res.json())
                .then(res => {
                    const tbody = document.querySelector("#srDataTable tbody");
                    tbody.innerHTML = "";

                    if (res.status === "success" && res.data.length) {
                        res.data.forEach(row => {
                            let tr = document.createElement("tr");
                            tr.innerHTML = `
                        <td class="border p-2">${row.date}</td>
                        <td class="border p-2">${row.name}</td>
                        <td class="border p-2">${row.srid}</td>
                        <td class="border p-2">${row.ofd}</td>
                        <td class="border p-2">${row.delivery}</td>
                        <td class="border p-2">${row.rate}</td>
                        <td class="border p-2">${row.fuel}</td>
                        <td class="border p-2">${row.advance}</td>
                        <td class="border p-2">${row.payment}</td>
                    `;
                            tbody.appendChild(tr);
                        });
                    } else {
                        tbody.innerHTML = `<tr><td colspan="9" class="text-center p-4 text-gray-500">No records found</td></tr>`;
                    }
                });
        }
        function loadSrList() {
    fetch('get_sr_list.php')
        .then(res => res.json())
        .then(data => {
            const srSelect = document.getElementById("srSelect");
            data.forEach(sr => {
                const option = document.createElement("option");
                option.value = sr.srid;
                option.text = `${sr.name} (ID: ${sr.srid})`;
                srSelect.appendChild(option);
            });
        })
        .catch(err => console.error("Error loading SR list:", err));
}
function loadSrList() {
    fetch('get_sr_list.php')
        .then(res => res.json())
        .then(data => {
            const srSelect = document.getElementById("srSelect");
            const seen = new Set();

            data.forEach(sr => {
                if (!seen.has(sr.srid)) {
                    seen.add(sr.srid);
                    const option = document.createElement("option");
                    option.value = sr.srid;
                    option.text = `${sr.name} (ID: ${sr.srid})`;
                    srSelect.appendChild(option);
                }
            });
        })
        .catch(err => console.error("Error loading SR list:", err));
}

    </script>


</body>

</html>