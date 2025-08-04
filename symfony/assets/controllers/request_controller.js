import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static targets = [
        "formContainer",
        "filterWrapper",
        "characterSearchWrapper",
        "filterSelectContainer",
        "filterInputContainer",
        "filterSelect",
        "filterInput",
        "searchInput",
        "seasonContainer",
        "seasonSelect",
        "episodeContainer",
        "episodeSelect",
        "submitButton",
        "loader",
        "resultsContainer",
        "characterList",
        "paginationContainer",
        "paginationInfo",
        "paginationButtonContainer",
        "prevButton",
        "nextButton",
        "fulfilledButton",
        "noRecords"
    ]

    connect() {
        fetch("/episodes")
            .then(response => response.json())
            .then(data => {
                this.episodes = data;
            })
            .catch(error => {
                console.error("Error fetching dimensions:", error);
                this.episodes = [];
            });
    }

    onTaskChange(event) {
        const value = event.target.value
        this.hide(this.characterSearchWrapperTarget);
        this.hide(this.filterWrapperTarget);
        this.hide(this.submitButtonTarget);
        this.filterCategory = null;

        if (value === "characters-filter") {
            this.filterSelectTarget.dispatchEvent(new Event("change", { bubbles: true }));
            this.show(this.filterWrapperTarget);
            this.hide(this.characterSearchWrapperTarget);
            this.hide(this.filterInputContainerTarget);
        } else if (value === "character-details") {
            this.filterCategory = "character";
            this.hide(this.filterWrapperTarget);
            this.show(this.characterSearchWrapperTarget);
        }
    }

    onCategoryChange(event) {
        const selected = event.target.value;
        this.filterCategory = selected;

        this.hide(this.seasonContainerTarget);
        this.hide(this.episodeContainerTarget);

        if (selected === "episode") {
            this.hide(this.filterInputContainerTarget);
            this.show(this.seasonContainerTarget);
            this.hide(this.episodeContainerTarget);

            this.seasonSelectTarget.replaceChildren();
            const placeholder = document.createElement("option");
            placeholder.textContent = "Choose Season";
            placeholder.disabled = true;
            placeholder.selected = true;

            this.seasonSelectTarget.appendChild(placeholder);
            for (const [seasonKey, seasonLabel] of Object.entries(this.episodes['seasons'])) {
                const option = document.createElement("option");
                option.value = seasonKey;
                option.textContent = seasonLabel;
                this.seasonSelectTarget.appendChild(option);
            }
        } else if (selected === "location" || selected === "dimension") {
            this.filterInputTarget.dataset.entity = selected;
            this.show(this.filterInputContainerTarget);
        }
    }

    onSeasonChange(event) {
        const selectedSeasonKey = event.target.value;
        const episodes = this.episodes['episodes'][selectedSeasonKey];

        this.episodeSelectTarget.replaceChildren();
        const placeholder = document.createElement("option");
        placeholder.textContent = "All Episodes";
        placeholder.value = "";
        placeholder.selected = true;

        this.episodeSelectTarget.appendChild(placeholder);
        episodes.forEach(ep => {
            const option = document.createElement("option");
            option.value = ep['episode'];
            option.textContent = ep['episode'] + ": " + ep['name'];
            this.episodeSelectTarget.appendChild(option);
        });

        this.show(this.episodeContainerTarget);
        this.show(this.submitButtonTarget);
    }

    submit(event) {
        event.preventDefault();
        this.hide(this.formContainerTarget);
        this.show(this.loaderTarget);
        this.fetchCharacters(1);
    }

    renderCharacters(data) {
        const heading = document.createElement("h2");
        heading.className = "text-xl font-semibold mb-4";
        heading.textContent = `Results for searching ${this.filterCategory}: ${data.search}`;

        this.characterListTarget.appendChild(heading);

        if (Array.isArray(data.characters) && data.characters.length > 0) {
            const grid = document.createElement("div");
            grid.className = "character-grid";

            data.characters.forEach(character => {
                const card = document.createElement("div");
                card.className = "character-card";
                card.dataset.action = "click->profile#showDetail";
                card.dataset.details = `${JSON.stringify(character)}`;

                const img = document.createElement("img");
                img.src = character.image;
                img.alt = character.name;
                card.appendChild(img);

                const h3 = document.createElement("h3");
                h3.textContent = character.name;
                h3.className = "text-md font-bold";
                card.appendChild(h3);

                const h6 = document.createElement("h6");
                h6.textContent = character.location.name;
                h6.className = "text-xs italic";
                card.appendChild(h6);

                grid.appendChild(card);
            });


            this.characterListTarget.appendChild(grid);
            this.hide(this.noRecordsTarget);
            this.updatePager(data.pagination);
            this.show(this.paginationContainerTarget);
        } else {
            this.show(this.noRecordsTarget);
            this.hide(this.paginationContainerTarget);
        }

        this.show(this.resultsContainerTarget);
    }

    updatePager(pagination) {
        const prevBtn = this.prevButtonTarget;
        const nextBtn = this.nextButtonTarget;

        this.resetPager(prevBtn);
        this.resetPager(nextBtn);

        if (pagination.previous == null && pagination.next == null) {
            this.hide(this.paginationButtonContainerTarget)
        } else {
            this.show(this.paginationButtonContainerTarget)

            if (pagination.previous != null) {
                prevBtn.disabled = false;
                prevBtn.dataset.page = pagination.previous;
                prevBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                prevBtn.classList.add('cursor-pointer')
            }

            if (pagination.next != null) {
                nextBtn.disabled = false;
                nextBtn.dataset.page = pagination.next;
                nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                nextBtn.classList.add('cursor-pointer')
            }
        }

        this.paginationInfoTarget.innerHTML = pagination.info;
    }

    resetPager (element) {
        element.disabled = true
        element.classList.add('opacity-50', 'cursor-not-allowed');
        element.classList.remove('cursor-pointer')
        element.removeAttribute('data-page');
    }

    loadPage(event) {
        const page = parseInt(event.currentTarget.dataset.page);
        this.fetchCharacters(page);
    }

    fetchCharacters(page = 1) {
        const url = this.buildQueryUrl(page);
        this.characterListTarget.innerHTML = "";
        this.show(this.loaderTarget);
        fetch(url)
            .then((response) => {
                if (!response.ok) throw new Error("Network response was not ok");
                return response.json();
            })
            .then((data) => {
                this.hide(this.loaderTarget);
                this.renderCharacters(data);
                this.show(this.fulfilledButtonTarget)
            })
            .catch((error) => {
                console.error("Error fetching characters:", error);
            });
    }

    buildQueryUrl(page = 1) {
        const params = new URLSearchParams();
        const category = this.filterCategory;
        const inputValue = this.filterInputTarget?.value;
        const searchValue = this.searchInputTarget.value;
        const season = this.seasonSelectTarget?.value;
        const episode = this.episodeSelectTarget?.value;

        if (category === "dimension") {
            params.set("dimensionName", inputValue || "");
        } else if (category === "location") {
            params.set("locationName", inputValue || "");
        } else if (category === "episode" && season) {
            params.set("season", season);
            if (episode) {
                params.set("episode", episode);
            }
        } else {
            params.set("characterName", searchValue || "");
        }

        if (page && page > 1) {
            params.set("page", page);
        }

        return `/character?${params.toString()}`;
    }

    show(target) {
        target.classList.remove("hidden");
    }

    hide(target) {
        target.classList.add("hidden");
    }
}
