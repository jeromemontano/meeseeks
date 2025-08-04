import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        "searchInput",
        "suggestionList",
        "submitButton"
    ];

    connect() {
        this.timeout = null;
        this.handleClickOutside = this.handleClickOutsideSuggestionList.bind(this);
        document.addEventListener("click", this.handleClickOutside);
    }

    disconnect() {
        document.removeEventListener("click", this.handleClickOutside);
    }

    onSearchInput(event) {
        const input = event.currentTarget;
        const list = this.getClosestElement(input, "suggestionList");
        const query = input.value.trim();
        const entity = input.dataset.entity;

        clearTimeout(this.timeout);

        if (!query) {
            this.submitButtonTarget.classList.add('hidden')
            this.hideSuggestions(list);
            return;
        } else {
            this.submitButtonTarget.classList.remove('hidden')
        }

        this.timeout = setTimeout(() => {
            this.fetchSuggestions(query, entity, list);
        }, 300);
    }

    fetchSuggestions(query, entity, list) {
        this.hideSuggestions(list);
        fetch(`/${entity}/autocomplete/?query=${encodeURIComponent(query)}`)
            .then((response) => {
                if (!response.ok) throw new Error("Autocomplete request failed.");
                return response.json();
            })
            .then((data) => {
                this.updateSuggestions(data, list);
            })
            .catch((error) => {
                console.error("Autocomplete error:", error);
                this.hideSuggestions(list);
            });
    }

    hideSuggestions(list) {
        list.innerHTML = "";
        list.classList.add("hidden");
    }

    updateSuggestions(suggestions, list) {
        if (!Array.isArray(suggestions) || suggestions.length === 0) {
            return;
        }

        suggestions.forEach(item => {
            const li = document.createElement("li");
            li.dataset.action = "click->autocomplete#selectSuggestion";
            li.dataset.value = item;
            li.className = "cursor-pointer px-3 py-1 hover:bg-gray-200";
            li.textContent = item;
            list.appendChild(li);
        });

        list.classList.remove("hidden");
        this.activeList = list
    }

    selectSuggestion(event) {
        const input = this.getClosestElement(event.currentTarget, "searchInput")
        input.value = event.currentTarget.dataset.value
        const list = this.getClosestElement(event.currentTarget, "suggestionList")
        this.hideSuggestions(list);
    }

    getClosestElement(element, target) {
        const inputGroup = element.closest(".input-group")
        return inputGroup.querySelector('[data-autocomplete-target="' + target +'"]')
    }

    handleClickOutsideSuggestionList(event) {
        if (!this.activeList) return;

        const clickedInsideList = this.activeList.contains(event.target);
        if (clickedInsideList) return;

        this.hideSuggestions(this.activeList);
    }
}
