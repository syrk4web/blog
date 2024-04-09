window.addEventListener("load", () => {
    // Get every label
    const labels = document.querySelectorAll("label");
    // Loop through each label
    labels.forEach(label => {
        // Get the div parent
        const parent = label.parentElement;
        // set class form-group to it
        parent.classList.add("form-group");
        // get input
        const input = parent.querySelector("input") || parent.querySelector("textarea") || null;
        // get type
        const type = input ? input.getAttribute("type") : false;

        if(input && type !== "checkbox" && type !== "radio") {
            // set class form-control to it
            input.classList.add("form-control");
            label.classList.add("form-label", "mt-2");
        }

        if(input && type === "checkbox") {
            // set class form-check to it
            input.classList.add("form-check-input");
            label.classList.add("form-check-label");
            parent.classList.add("form-check", "mt-4");
        }

        const select = parent.querySelector("select") || null;
        if(select) {
            select.classList.add("form-select");
            label.classList.add("form-label", "mt-2");
        }
    });

    // Get submit btn
    const submitBtns = document.querySelectorAll("button[type='submit']");
    // Loop through each submit btn
    submitBtns.forEach(submitBtn => {
        // set class btn btn-primary to it
        submitBtn.className = "btn btn-primary mt-4";
    });
})