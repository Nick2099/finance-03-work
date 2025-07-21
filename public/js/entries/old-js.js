// *************************************************
        // Recurring entry logic
        // *************************************************

        const recurrencySelect = document.getElementById('recurrency');
        const frequenvySelect = document.getElementById('frequency');
        const dayOfMonthInput = document.getElementById('day-of-month');
        const dayOfWeekInput = document.getElementById('day-of-week');
        const lastsInput = document.getElementById('lasts');
        const givenEndDateInput = document.getElementById('given-end-date');

        // Helper to get the 'day' property of the selected frequency option
        let selectedFrequencyDay = null;

        function updateFrequencyOptions() {
            if (!recurrencySelect || !frequenvySelect) return;

            const selectedRecurrency = recurrencySelect.value;
            frequenvySelect.innerHTML = '';

            // Find the config for the selected recurrency (e.g., 'month' or 'week')
            const config = recurringMenuConfig[selectedRecurrency];
            if (config && config.options) {
                Object.entries(config.options).forEach(([key, opt]) => {
                    const option = document.createElement('option');
                    option.value = key;
                    // Use translation if available, otherwise fallback to label
                    option.text = frequencyTranslations[opt.label] || opt.label;
                    frequenvySelect.appendChild(option);
                });
                // Set selectedFrequencyDay for the first option (default selected)
                const firstKey = Object.keys(config.options)[0];
                selectedFrequencyDay = config.options[firstKey]?.day ?? null;
                updateDayOfMonthVisibility();
            }
        }

        function updateDayOfMonthVisibility() {
            if (!recurrencySelect || !frequenvySelect) return;

            const selectedRecurrency = recurrencySelect.value;
            const config = recurringMenuConfig[selectedRecurrency];

            if (config && config.options) {
                const selectedKey = frequenvySelect.value;
                selectedFrequencyDay = config.options[selectedKey]?.day ?? null;
            } else {
                selectedFrequencyDay = null;
            }
            
            const dayOfMonth = document.getElementById('day-of-month-wrapper');
            const dayOfWeek = document.getElementById('day-of-week-wrapper');
            if (!dayOfMonth || !dayOfWeek) return;
            if (selectedFrequencyDay && selectedRecurrency === 'month') {
                dayOfMonth.style.display = 'block';
            } else {
                dayOfMonth.style.display = 'none';
            }
            if (selectedFrequencyDay && selectedRecurrency === 'week') {
                dayOfWeek.style.display = 'block';
            } else {
                dayOfWeek.style.display = 'none';
            }
            updateStartDate();
        }

        if (recurrencySelect && frequenvySelect) {
            recurrencySelect.addEventListener('change', updateFrequencyOptions);
            frequenvySelect.addEventListener('change', updateDayOfMonthVisibility);
            updateFrequencyOptions();
        }

        if (dayOfMonthInput) {
            dayOfMonthInput.addEventListener('input', updateStartDate);
        }

        if (dayOfWeekInput) {
            dayOfWeekInput.addEventListener('input', updateStartDate);
        }

        if (recurring) {
            document.getElementById('date').addEventListener('input', updateStartDate);
        }

        if (lastsInput) {
            lastsInput.addEventListener('input', showGivenEndDate);
            lastsInput.addEventListener('input', updateEndDate);
            showGivenEndDate();
        }

        if (givenEndDateInput) {
            givenEndDateInput.addEventListener('input', updateEndDate);
        }    

        // *************************************************
        // help functions for date calculations
        // *************************************************
        
        // Convert string to Date object - checked, in use
        function stringToDate(tmpDate) {
            if (!(tmpDate instanceof Date) || isNaN(tmpDate)) {
                tmpDate = new Date(tmpDate);
            }    
            return tmpDate;
        }    

        // Get the last day of the month for a given date - checked, in use
        function getLastDayOfMonth(tmpDate) {
            let year = tmpDate.getFullYear();
            let month = tmpDate.getMonth();
            let date = new Date(year, month + 1, 0);
            let day = date.getDate();
            return day;
        }    
        
        function newDate(date) {
            const pad = n => n < 10 ? '0' + n : n;
            const newDateValue = `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
            return newDateValue;
        }

        // Change the day in a date, ensuring it does not exceed the last day of the month - checked, in use
        function changeDayInDate(date, day) {
            day = parseInt(day, 10);
            let lastDay = getLastDayOfMonth(date);
            if (day > lastDay) {
                day = lastDay;
            }
            const newDate = new Date(date);
            if (day) {
                newDate.setDate(parseInt(day, 10));
            }
            return newDate;
        }

        // Change the date to the first day of the month - not in use
        /*
        function changeToLastDayInMonth(date) {
            const lastDay = getLastDayOfMonth(date);
            const newDate = new Date(date);
            newDate.setDate(lastDay);
            return newDate;
        }
        */

        // Get the first day of the week (Monday) for a given date - checked, in use
        function firstDayOfWeek(date) {
            const day = date.getDay();
            if (day === 0) {
                date.setDate(date.getDate() - 7);
            };
            const firstDay = new Date(date);
            firstDay.setDate(firstDay.getDate() - firstDay.getDay() + 1); // Set to Monday
            return firstDay;
        }

        // Add days to a date - checked, in use
        function addDays(date, days) {
            days = days - 1; // Adjust for the first day being counted as 1
            const newDate = new Date(date);
            newDate.setDate(newDate.getDate() + days);
            return newDate;
        }

        // Update end date based on lasts and given end date - checked, in use
        function showGivenEndDate() {
            const lastsValue = document.getElementById('lasts').value;
            const giveEndDateWrapper = document.getElementById('given-end-date-wrapper');
            if (lastsValue === '2') { // Custom date
                giveEndDateWrapper.style.display = 'block';
            } else {
                giveEndDateWrapper.style.display = 'none';
            }
            // updateEndDate();
        }

        // Update end date based on lasts and given end date - checked, in use
        function lastDayInYear(date) {
            const d = new Date(date); // convert string to Date
            const year = d.getFullYear();
            return new Date(year, 11, 31); // December 31st of the same year
        }

        // Get the last day of the year by a given day of the week - checked, in use
        function lastDayInYearByDay(date, requestedDayOfWeek) {
            requestedDayOfWeek = parseInt(requestedDayOfWeek, 10);
            if (requestedDayOfWeek == 7) {
                requestedDayOfWeek = 0; // Convert 7 to 0 for Sunday
            }
            while (date.getDay() !== requestedDayOfWeek) {
                date.setDate(date.getDate() - 1);
            }
            return date;
        }

        // *************************************************
        // recurrency functions
        // *************************************************

        // Get the first working day of the month - checked, in use
        function firstWorkingDayOfMonth(tmpDate) {
            let year = tmpDate.getFullYear();
            let month = tmpDate.getMonth();
            let date = new Date(year, month, 1);
            while (date.getDay() === 0 || date.getDay() === 6) { // 0 = Sunday, 6 = Saturday
                date.setDate(date.getDate() + 1);
            }        
            return date;
        }        

        // Get the last working day of the month - checked, in use
        function lastWorkingDayOfMonth(tmpDate) {
            let year = tmpDate.getFullYear();
            let month = tmpDate.getMonth();
            let date = new Date(year, month + 1, 0);
            while (date.getDay() === 0 || date.getDay() === 6) { // 0 = Sunday, 6 = Saturday
                date.setDate(date.getDate() - 1);    
            }    
            return date;
        }        

        // Get the exact day of the month - checked, in use
        function exactDayOfMonth(startDate, dayOfMonthInputValue) {
            const lastDay = getLastDayOfMonth(new Date(startDate));
            if (lastDay < dayOfMonthInputValue) {
                dayOfMonthInputValue = lastDay;
            }
            const date = new Date(startDate);
            date.setDate(parseInt(dayOfMonthInputValue, 10));
            return date;
        }    

        // Get the first working day on or after a given date - checked, in use
        function firstWorkingDayOnOrAfter(tmpDate) {
            let date = new Date(tmpDate);
            let tmpMonth = date.getMonth();
            while (date.getDay() === 0 || date.getDay() === 6) {
                date.setDate(date.getDate() + 1);
            }
            if (date.getMonth() > tmpMonth) {
                date = lastWorkingDayOfMonth(new Date(tmpDate));
            }
            return date;
        }    

        // Get the last working day on or before a given date - checked, in use
        function lastWorkingDayOnOrBefore(tmpDate) {
            let date = new Date(tmpDate);
            let tmpMonth = date.getMonth();
            while (date.getDay() === 0 || date.getDay() === 6) {
                date.setDate(date.getDate() - 1);
            }
            if (date.getMonth() < tmpMonth) {
                date = firstWorkingDayOfMonth(new Date(tmpDate));
            }
            return date;
        }    

        // Update start date on recurrency and frequency
        function updateStartDate() {
            console.log('Updating start date');

            const dateValue = document.getElementById('date').value;
            const recurrencySelectValue = document.getElementById('recurrency').value;
            const frequencySelectValue = document.getElementById('frequency').value;
            const dayOfMonthInputValue = document.getElementById('day-of-month').value;
            const dayOfWeekInputValue = document.getElementById('day-of-week').value;
            const startDate = new Date(dateValue);

            if (recurrencySelectValue === 'month') {
                if (frequencySelectValue === '1') { // First working day of the month
                    const firstDay = firstWorkingDayOfMonth(startDate);
                    const newDateValue = newDate(firstDay);
                    document.getElementById('start-date').value = newDateValue;
                } else if (frequencySelectValue === '2') { // Last working day of the month
                    const lastDay = lastWorkingDayOfMonth(startDate);
                    const newDateValue = newDate(lastDay);
                    document.getElementById('start-date').value = newDateValue;
                } else if (frequencySelectValue === '3') { // Day of month
                    const exactDay = exactDayOfMonth(startDate, dayOfMonthInputValue);
                    const newDateValue = newDate(exactDay);
                    document.getElementById('start-date').value = newDateValue;
                } else if (frequencySelectValue === '4') { // First working day on or after
                    const indexDate = changeDayInDate(startDate, dayOfMonthInputValue);
                    const correctedDate = firstWorkingDayOnOrAfter(indexDate);
                    const newDateValue = newDate(correctedDate);
                    document.getElementById('start-date').value = newDateValue;
                } else if (frequencySelectValue === '5') { // Last working day on or before
                    const indexDate = changeDayInDate(startDate, dayOfMonthInputValue);
                    const correctedDate = lastWorkingDayOnOrBefore(indexDate);
                    const newDateValue = newDate(correctedDate);
                    document.getElementById('start-date').value = newDateValue;
                } 
            } else if (recurrencySelectValue === 'week') {
                if (frequencySelectValue === '1' || frequencySelectValue === '2') { // Every week on or every 2 weeks
                    const dayOfWeek = parseInt(dayOfWeekInputValue, 10);
                    const firstDay = firstDayOfWeek(startDate); 
                    const newDateValue = newDate(addDays(firstDay, dayOfWeek));
                    document.getElementById('start-date').value = newDateValue;
                };
            }
            updateEndDate();
        }

        // Update end date based on recurrency and frequency
        function updateEndDate() {
            console.log('Updating end date');

            const dateValue = document.getElementById('date').value;
            const recurrencySelectValue = document.getElementById('recurrency').value;
            const frequencySelectValue = document.getElementById('frequency').value;
            const dayOfMonthInputValue = document.getElementById('day-of-month').value;
            const dayOfWeekInputValue = document.getElementById('day-of-week').value;
            const lastsForValue = document.getElementById('lasts').value;

            let lastDate = '';
            if (lastsForValue === '1') { // until end of the year
                lastDate = lastDayInYear(dateValue);
            } else if (lastsForValue === '2') { // custom date
                lastDate = document.getElementById('given-end-date').value;
            }
            lastDate = stringToDate(lastDate);

            if (recurrencySelectValue === 'month') {
                if (frequencySelectValue === '1') { // First working day of the month
                    const firstDay = firstWorkingDayOfMonth(lastDate);
                    const newDateValue = newDate(firstDay);
                    document.getElementById('end-date').value = newDateValue;
                } else if (frequencySelectValue === '2') { // Last working day of the month
                    const lastDay = lastWorkingDayOfMonth(lastDate);
                    const newDateValue = newDate(lastDay);
                    document.getElementById('end-date').value = newDateValue;
                } else if (frequencySelectValue === '3') { // Day of month
                    const exactDay = exactDayOfMonth(lastDate, dayOfMonthInputValue);
                    const newDateValue = newDate(exactDay);
                    document.getElementById('end-date').value = newDateValue;
                } else if (frequencySelectValue === '4') { // First working day on or after
                    // const startDate = new Date(dateValue);
                    const indexDate = changeDayInDate(lastDate, dayOfMonthInputValue);
                    const correctedDate = firstWorkingDayOnOrAfter(indexDate);
                    const newDateValue = newDate(correctedDate);
                    document.getElementById('end-date').value = newDateValue;
                } else if (frequencySelectValue === '5') { // Last working day on or before
                    // const startDate = new Date(dateValue);
                    const indexDate = changeDayInDate(lastDate, dayOfMonthInputValue);
                    const correctedDate = lastWorkingDayOnOrBefore(indexDate);
                    const newDateValue = newDate(correctedDate);
                    document.getElementById('end-date').value = newDateValue;
                } 
            } else if (recurrencySelectValue === 'week') {
                if (frequencySelectValue === '1' || frequencySelectValue === '2') { // Every week on every 2 weeks
                    if (lastsForValue === '1') { // until end of the year
                        const exactDay = lastDayInYearByDay(lastDate, dayOfWeekInputValue);
                        const newDateValue = newDate(exactDay);
                        document.getElementById('end-date').value = newDateValue;
                    } else if (lastsForValue === '2') { // custom date
                        const dayOfWeek = parseInt(dayOfWeekInputValue, 10);
                        const exactDay = firstDayOfWeek(lastDate); 
                        const newDateValue = newDate(addDays(exactDay, dayOfWeek));
                        document.getElementById('end-date').value = newDateValue;
                    }
                };
            }
        }