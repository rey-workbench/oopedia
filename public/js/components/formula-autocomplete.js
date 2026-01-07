class FormulaAutocomplete {
    constructor(inputId, data) {
        this.input = document.getElementById(inputId);
        if (!this.input) return;

        this.data = data; // Array of { key, label, type, snippet }
        this.currentFocus = -1;
        this.container = null;

        // Add Event Listeners
        this.input.addEventListener("input", this.onInput.bind(this));
        this.input.addEventListener("keydown", this.onKeyDown.bind(this));
        document.addEventListener("click", this.closeAllLists.bind(this));

        // Wrap input in relative container if not already
        const wrapper = document.createElement('div');
        wrapper.style.position = 'relative';
        this.input.parentNode.insertBefore(wrapper, this.input);
        wrapper.appendChild(this.input);
        this.wrapper = wrapper;
    }

    onInput(e) {
        let val = this.getCurrentWord();
        this.closeAllLists();
        if (!val || val.length < 1) return false;

        this.currentFocus = -1;

        // Create container
        this.container = document.createElement("div");
        this.container.setAttribute("id", this.input.id + "autocomplete-list");
        this.container.setAttribute("class", "autocomplete-items");

        this.wrapper.appendChild(this.container);

        let matchCount = 0;
        for (let i = 0; i < this.data.length; i++) {
            // Check if item starts with current word or contains it
            if (this.data[i].key.toLowerCase().includes(val.toLowerCase())) {
                matchCount++;
                if (matchCount > 10) break; // Limit results

                let item = document.createElement("div");
                item.className = "autocomplete-item";

                // Highlight match
                const regex = new RegExp(`(${val})`, "gi");
                const highlighted = this.data[i].key.replace(regex, "<strong>$1</strong>");

                item.innerHTML = `
                    <span>${highlighted}</span>
                    <span class="autocomplete-type ${this.data[i].type.toLowerCase()}">${this.data[i].type}</span>
                `;

                item.innerHTML += `<input type='hidden' value='${this.data[i].key}'>`;

                item.addEventListener("click", (e) => {
                    this.insertValue(this.data[i]);
                    this.closeAllLists();
                });

                this.container.appendChild(item);
            }
        }
    }

    onKeyDown(e) {
        let x = document.getElementById(this.input.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) { // Down
            this.currentFocus++;
            this.addActive(x);
        } else if (e.keyCode == 38) { // Up
            this.currentFocus--;
            this.addActive(x);
        } else if (e.keyCode == 13 || e.keyCode == 9) { // Enter or Tab
            if (this.currentFocus > -1) {
                if (x) x[this.currentFocus].click();
                e.preventDefault(); // Prevent form submit or newline/tab nav
            } else if (e.keyCode == 9 && x && x.length > 0) {
                // If tab pressed and list exists but no focus, select first
                x[0].click();
                e.preventDefault();
            }
        }
    }

    addActive(x) {
        if (!x) return false;
        this.removeActive(x);
        if (this.currentFocus >= x.length) this.currentFocus = 0;
        if (this.currentFocus < 0) this.currentFocus = (x.length - 1);
        x[this.currentFocus].classList.add("autocomplete-active");
        x[this.currentFocus].scrollIntoView({ block: 'nearest' });
    }

    removeActive(x) {
        for (let i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
        }
    }

    closeAllLists(elmnt) {
        let x = document.getElementsByClassName("autocomplete-items");
        for (let i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != this.input) {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }

    getCurrentWord() {
        // Get word at cursor position
        const text = this.input.value;
        const pos = this.input.selectionStart;

        // Find beginning of word
        let start = pos;
        while (start > 0 && /[\w]/.test(text.charAt(start - 1))) {
            start--;
        }

        // Find end of word
        let end = pos;
        while (end < text.length && /[\w]/.test(text.charAt(end))) {
            end++;
        }

        return text.substring(start, end);
    }

    insertValue(itemData) {
        const text = this.input.value;
        const pos = this.input.selectionStart;

        // Use snippet if available, otherwise key
        const insertText = itemData.snippet || itemData.key;

        // Find beginning of word to replace
        let start = pos;
        while (start > 0 && /[\w]/.test(text.charAt(start - 1))) {
            start--;
        }

        // Find end
        let end = pos;
        while (end < text.length && /[\w]/.test(text.charAt(end))) {
            end++;
        }

        const newText = text.substring(0, start) + insertText + text.substring(end);
        this.input.value = newText;

        // Focus and Cursor Positioning
        this.input.focus();

        // Check for placeholders (basic logic: defined inside parentheses)
        // Example: PERCENTAGE(value, total) -> select "value"
        if (itemData.snippet && itemData.snippet.includes('(')) {
            const firstParen = start + itemData.snippet.indexOf('(');
            const comma = start + itemData.snippet.indexOf(',');
            const closingParen = start + itemData.snippet.indexOf(')');

            // Determine end of first argument (either comma or closing paren)
            let endOfArg = -1;
            if (comma > -1 && closingParen > -1) endOfArg = Math.min(comma, closingParen);
            else if (comma > -1) endOfArg = comma;
            else endOfArg = closingParen;

            if (firstParen > -1 && endOfArg > firstParen) {
                // Select the first argument
                this.input.setSelectionRange(firstParen + 1, endOfArg);
                return;
            }
        }

        // Default: cursor at end of inserted text
        const newPos = start + insertText.length;
        this.input.setSelectionRange(newPos, newPos);
    }
}
