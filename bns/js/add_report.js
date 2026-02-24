// ===================== MAIN SCRIPT =====================
document.addEventListener("DOMContentLoaded", function () {
    // --------------------- ELEMENTS ---------------------
    const form = document.querySelector("form");
    const submitBtn = document.querySelector(".submit-btn");
    const requiredInputs = document.querySelectorAll("input[required]");
    const csvFileInput = document.getElementById("csvFile");

    const totalInput = document.getElementById("total");
    const maleInput = document.getElementById("male");
    const femaleInput = document.getElementById("female");
    const titleInput = document.getElementById("report-title");

    // --------------------- UTILITY FUNCTIONS ---------------------
function disableInputs() {
    requiredInputs.forEach(input => {
        input.readOnly = true;              // values will be submitted
        input.classList.add('bg-gray-100','cursor-not-allowed'); // visual lock
    });
}

function enableInputs() {
    requiredInputs.forEach(input => {
        input.readOnly = false;
        input.classList.remove('bg-gray-100','cursor-not-allowed');
    });
}

    function clearBorders() {
        requiredInputs.forEach(input => input.style.border = "");
    }

    // --------------------- CHECK COMPLETION ---------------------
    function checkCompletion() {
        let allFilled = true;
        requiredInputs.forEach(input => {
            if (input.value.trim() === "") allFilled = false;
        });
        submitBtn.disabled = !allFilled;
    }

    requiredInputs.forEach(input => input.addEventListener("input", checkCompletion));
    checkCompletion(); // initial check on page load

    // --------------------- CSV IMPORT ---------------------
    csvFileInput.addEventListener("change", function(e) {
        const file = e.target.files[0];
        if (!file) return;

        enableInputs();
        clearBorders();

        const reader = new FileReader();
        reader.onload = function(event) {
            const text = event.target.result;
            const rows = text.split(/\r?\n/).filter(r => r.trim() !== "");
            let values = [];

            rows.forEach(row => {
                const cols = row.split(/\t|,/);
                cols.forEach(cell => {
                    let val = cell.trim();
                    if (val.endsWith("%")) val = val.replace("%", "");
                    if (val !== "" && !isNaN(val)) values.push(val);
                });
            });

            const inputMapping = [
                'ind1','ind_male','ind_female','ind2','ind3','ind4','ind5',
                'ind6a','ind6b','ind7','ind8','ind9','ind9a',
                'ind9b1_no','ind9b1_pct','ind9b2_no','ind9b2_pct','ind9b3_no','ind9b3_pct',
                'ind9b4_no','ind9b4_pct','ind9b5_no','ind9b5_pct','ind9b6_no','ind9b6_pct',
                'ind9b7_no','ind9b7_pct','ind9b8_no','ind9b8_pct','ind9b9_no','ind9b9_pct',
                'ind10','ind11','ind12','ind13','ind14','ind15','ind16',
                'ind17a_public','ind17a_private','ind17b_public','ind17b_private',
                'ind18','ind19','ind20','ind21',
                'ind22a_no','ind22a_pct','ind22b_no','ind22b_pct','ind22c_no','ind22c_pct',
                'ind22d_no','ind22d_pct','ind22e_no','ind22e_pct','ind22f_no','ind22f_pct',
                'ind22g_no','ind22g_pct',
                'ind23','ind24','ind25','ind26',
                'ind27a_no','ind27a_pct','ind27b_no','ind27b_pct','ind27c_no','ind27c_pct',
                'ind27d_no','ind27d_pct','ind27e_no','ind27e_pct',
                'ind28a_no','ind28a_pct','ind28b_no','ind28b_pct','ind28c_no','ind28c_pct',
                'ind28d_no','ind28d_pct',
                'ind29a_no','ind29a_pct','ind29b_no','ind29b_pct','ind29c_no','ind29c_pct',
                'ind29d_no','ind29d_pct','ind29e_no','ind29e_pct','ind29f_no','ind29f_pct',
                'ind29g_no','ind29g_pct',
                'ind30a_no','ind30a_pct','ind30b_no','ind30b_pct','ind30c_no','ind30c_pct',
                'ind30d_no','ind30d_pct',
                'ind31a_no','ind31a_pct','ind31b_no','ind31b_pct','ind31c_no','ind31c_pct',
                'ind31d_no','ind31d_pct','ind31e_no','ind31e_pct','ind31f_no','ind31f_pct',
                'ind32_no','ind32_pct',
                'ind33_no','ind33_pct',
                'ind34_no','ind34_pct',
                'ind35_no','ind35_pct',
                'ind36_no','ind36_pct',
                'ind37a','ind37b','ind38'
            ];

            let valueIndex = 0;
            let missingFields = false;

            inputMapping.forEach(name => {
                const el = document.querySelector(`[name="${name}"]`);
                if (!el) return;

                let val = values[valueIndex] || '';
                el.value = val;

                if (val === '') {
                    missingFields = true;
                    el.style.border = "2px solid red";
                } else {
                    el.style.border = "";
                }

                valueIndex++;
            });

            disableInputs(); // lock all inputs after import
            submitBtn.disabled = missingFields; // only enable if complete

            if (missingFields) {
                alert("⚠ Some fields are missing. Submit is disabled.");
            } else {
                alert("✅ Import successful. Submit is enabled.");
            }
        };

        reader.readAsText(file);
    });

    // --------------------- POPULATION FIELD SYNC ---------------------
    let isUpdating = false;
    function updateTotalFromParts() {
        if (isUpdating) return;
        isUpdating = true;
        const male = parseInt(maleInput.value) || 0;
        const female = parseInt(femaleInput.value) || 0;
        totalInput.value = male + female;
        isUpdating = false;
    }

    maleInput.addEventListener("input", updateTotalFromParts);
    femaleInput.addEventListener("input", updateTotalFromParts);

    // --------------------- AUTO-FILL TITLE ---------------------
    if (titleInput && !titleInput.value.trim()) {
        const now = new Date();
        const month = now.toLocaleString("default", { month: "long" });
        const date = now.getDate();
        const barangayInput = document.querySelector('[name="barangay"]');
        const barangay = barangayInput && barangayInput.value.trim() ? "Barangay " + barangayInput.value.trim() : "";
        titleInput.value = barangay ? `New Report: ${barangay} – ${month} ${date}` : `New Report: ${month} ${date}`;
    }

    // --------------------- HIDDEN TITLE ON SUBMIT ---------------------
    form.addEventListener("submit", function () {
        let oldHidden = form.querySelector('input[name="title"]');
        if (oldHidden) oldHidden.remove();

        const hiddenTitle = document.createElement("input");
        hiddenTitle.type = "hidden";
        hiddenTitle.name = "title";
        hiddenTitle.value = titleInput.value;
        form.appendChild(hiddenTitle);
    });
});
