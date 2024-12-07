document.addEventListener('DOMContentLoaded', function () {
    comboSelect('#filtro_medico', filter_ag_req);
    comboSelect('#filtro_paciente', filter_ag_req);
    comboSelect('#filtro_status', filter_ag_req);
    comboSelect('#filtro_modalidade', filter_ag_req);
});


function pagination() {}

const comboSelect = function (selectBox, selectedEvent = null) {
    const customSelect = document.querySelector(selectBox);
    if (customSelect) {
        const selectSelected = customSelect.querySelector('.select-selected');
        const selectItems = customSelect.querySelector('.select-items');
        const selectArrow = customSelect.querySelector('.select-arrow');
        const searchInput = customSelect.querySelector('.search-input');
        const optionsList = customSelect.querySelector('.options-list');
        const options = optionsList.querySelectorAll('li');
        const input = document.createElement('input');


        if ((customSelect) && ('id' in customSelect)) {
            input.type = 'hidden';
            input.name = ('value' in customSelect.dataset) ? customSelect.dataset.value : customSelect.id;
            customSelect.appendChild(input);
        }
        if (selectSelected) 
            selectSelected.addEventListener('click', toggleDropdown);
        


        if (customSelect) 
            document.addEventListener('click', (e) => {
                if (! customSelect.contains(e.target)) { 
                    closeDropdown();
                }
            });
        


        if (searchInput) 
            searchInput.addEventListener('input', filterOptions);
        


        if ((options) && (selectSelected)) 
            options.forEach(option => {
                option.addEventListener('click', () => {
                    selectSelected.textContent = option.textContent;
                    input.value = option.dataset.value;

                    closeDropdown();

                    if (selectedEvent !== null) {
                        selectedEvent(option);
                    }
                });
            });
        


        function toggleDropdown() {
            if (selectItems) {
                const isOpen = selectItems.style.display === 'block';
                selectItems.style.display = isOpen ? 'none' : 'block';
                selectArrow.classList.toggle('open', ! isOpen);
            }
        }
        
        function closeDropdown() {
            if (selectItems) {
                selectItems.style.display = 'none';
                selectArrow.classList.remove('open');
            }
        }


        function filterOptions() {
            const searchTerm = searchInput.value.toLowerCase();
            options.forEach(option => {
                const optionText = option.textContent.toLowerCase();
                if (optionText.includes(searchTerm)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
        }
    }
}
