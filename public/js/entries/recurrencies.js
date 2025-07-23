// *************************************************
// Recurring entry logic
// *************************************************

// elements needed for recurrencies menus
const baseElement = document.getElementById("base");
const frequencyElement = document.getElementById("frequency");
const ruleElement = document.getElementById("rule");
const dayOfMonthElement = document.getElementById("day-of-month");
const dayOfMonthWrapper = document.getElementById("day-of-month-wrapper");
const dayOfWeekElement = document.getElementById("day-of-week");
const dayOfWeekWrapper = document.getElementById("day-of-week-wrapper");
const monthElement = document.getElementById("month");
const monthWrapper = document.getElementById("month-wrapper");
const fromDateElement = document.getElementById("from-date");
const fromDateWrapper = document.getElementById("from-date-wrapper");
const numberOfOccurrencesElement = document.getElementById(
    "number-of-occurrences"
);
const occurrencesEndDateElement = document.getElementById(
    "occurrences-end-date"
);
const occurrencesEndDateWrapper = document.getElementById(
    "occurrences-end-date-wrapper"
);
const occurrencesNumberElement = document.getElementById("occurrences-number");
const occurrencesNumberWrapper = document.getElementById(
    "occurrences-number-wrapper"
);

// elements needed for output of start and end dates
const dateElement = document.getElementById("date");
const startDateElement = document.getElementById("start-date");
const endDateElement = document.getElementById("end-date");

// *****************************************************************
// Main logic for updating options and visibility of elements
// *****************************************************************
function updateFrequencyOptions() {
    if (!baseElement || !frequencyElement) return;

    const selectedBase = baseElement.value;
    frequencyElement.innerHTML = "";

    if (
        recurringMenuNew["base"] &&
        recurringMenuNew["base"][selectedBase] &&
        recurringMenuNew["base"][selectedBase]["frequency"]
    ) {
        Object.entries(
            recurringMenuNew["base"][selectedBase]["frequency"]
        ).forEach(([key, opt]) => {
            const option = document.createElement("option");
            option.value = key;
            option.text = frequencyTranslations[opt];
            frequencyElement.appendChild(option);
        });
    }
    updateRuleOptions(); // Update rules based on the new frequency options
}

function updateRuleOptions() {
    if (!frequencyElement || !ruleElement) return;

    const selectedBase = baseElement.value;
    const selectedFrequency = frequencyElement.value;
    ruleElement.innerHTML = "";

    if (
        recurringMenuNew["base"] &&
        recurringMenuNew["base"][selectedBase] &&
        recurringMenuNew["base"][selectedBase]["frequency"]
    ) {
        Object.entries(recurringMenuNew["base"][selectedBase]["rule"]).forEach(
            ([key, opt]) => {
                const option = document.createElement("option");
                option.value = key;
                let label = opt.label || key; // Use 'label' if available, otherwise use the key
                option.text = ruleTranslations[label] || label; // Use 'label' if available, otherwise use the key
                ruleElement.appendChild(option);
            }
        );
        updateVisibilityOfOtherRuleElements();
    }
}

function updateVisibilityOfOtherRuleElements() {
    if (!baseElement || !ruleElement) return;

    const selectedBase = baseElement.value;
    const selectedRule = ruleElement.value;
    let elementsToShow =
        recurringMenuNew["base"][selectedBase]["rule"][selectedRule] ||
        undefined;

    if (elementsToShow === undefined) return;

    if (dayOfMonthElement) {
        showHideElement(dayOfMonthWrapper, elementsToShow["day-of-month"]);
    }
    if (dayOfWeekElement) {
        showHideElement(dayOfWeekWrapper, elementsToShow["day-of-week"]);
    }
    if (monthElement) {
        showHideElement(monthWrapper, elementsToShow["month"]);
    }
    if (fromDateElement) {
        showHideElement(fromDateWrapper, elementsToShow["from-date"]);
    }
    getRecurrencyParameters(); // Update the rule with parameters after visibility changes
}

function updateVisibilityOfOccurrencesElements() {
    if (!numberOfOccurrencesElement) return;

    const selectedNumberOfOccurrences = numberOfOccurrencesElement.value;

    const elementsToShow =
        recurringMenuNew["number-of-occurrences"][
            selectedNumberOfOccurrences
        ] || undefined;
    if (elementsToShow === undefined) return;

    if (occurrencesEndDateElement) {
        showHideElement(occurrencesEndDateWrapper, elementsToShow["date"]);
    }
    if (occurrencesNumberElement) {
        showHideElement(occurrencesNumberWrapper, elementsToShow["number"]);
    }
    getRecurrencyParameters(); // Update the rule with parameters after visibility changes
}

function getRecurrencyParameters() {
    let recurrency = [];

    recurrency["entry-date"] = dateElement ? dateElement.value : null;
    recurrency["base"] = baseElement ? baseElement.value : null;
    recurrency["frequency"] = frequencyElement ? frequencyElement.value : null;
    recurrency["rule"] = ruleElement ? ruleElement.value : null;
    recurrency["day-of-month"] = dayOfMonthElement
        ? dayOfMonthElement.value
        : null;
    recurrency["day-of-week"] = dayOfWeekElement
        ? dayOfWeekElement.value
        : null;
    recurrency["month"] = monthElement ? monthElement.value : null;
    recurrency["from-date"] = fromDateElement ? fromDateElement.value : null;
    recurrency["number-of-occurrences"] = numberOfOccurrencesElement
        ? numberOfOccurrencesElement.value
        : null;
    recurrency["occurrences-number"] = occurrencesNumberElement
        ? occurrencesNumberElement.value
        : null;
    recurrency["occurrences-end-date"] = occurrencesEndDateElement
        ? occurrencesEndDateElement.value
        : null;
    console.log("Recurrency parameters:", recurrency);

    updateStartAndEndDates(recurrency);
}

function updateStartAndEndDates(recurrency) {
    if (!startDateElement || !endDateElement) return;

    if (recurrency["base"] == "week") {
        // calculate start date for weekly recurrency
        let fromDate = recurrency["from-date"]
            ? new Date(recurrency["from-date"])
            : new Date(dateElement.value);
        let startDate = new Date(fromDate);
        let dayOfWeek = parseInt(recurrency["day-of-week"], 10);
        while (startDate.getDay() !== dayOfWeek) {
            startDate.setDate(startDate.getDate() + 1);
        }
        startDateElement.value = startDate.toISOString().split("T")[0];

        // calculate end date for weekly recurrency
        let endDate = new Date(startDate);
        let step = parseInt(recurrency["frequency"], 10) * 7; // step in days
        let allDates = [];

        if (parseInt(recurrency["number-of-occurrences"]) == 1) {
            // case when the number of occurrences is given
            allDates.push(startDate.toISOString().split("T")[0]);
            for (
                let j = 1;
                j < parseInt(occurrencesNumberElement.value, 10);
                j++
            ) {
                let occurrenceDate = new Date(startDate);
                occurrenceDate.setDate(occurrenceDate.getDate() + step * j);
                if (!isNaN(occurrenceDate.getTime())) {
                    allDates.push(occurrenceDate.toISOString().split("T")[0]);
                } else {
                    console.warn("Invalid occurrenceDate:", occurrenceDate);
                }
            }
            endDate = new Date(allDates[allDates.length - 1]);
            endDateElement.value = endDate.toISOString().split("T")[0];
        } else if (
            parseInt(recurrency["number-of-occurrences"]) == 2 ||
            parseInt(recurrency["number-of-occurrences"]) == 3
        ) {
            // case when the end date is given or for unlimited recurrency
            endDate = new Date(occurrencesEndDateElement.value);
            // For unlimited recurrency, set end date to the end of the year three years in the future
            if (parseInt(recurrency["number-of-occurrences"]) == 3) {
                endDate = lastDayInYear(new Date(), 3);
            }
            let occurrenceDate = new Date(startDate);
            while (endDate >= occurrenceDate) {
                allDates.push(occurrenceDate.toISOString().split("T")[0]);
                occurrenceDate.setDate(occurrenceDate.getDate() + step);
            }
            endDate = new Date(allDates[allDates.length - 1]);
            endDateElement.value = endDate.toISOString().split("T")[0];
        }
        console.log("All weekly occurrence dates:", allDates);
    } else if (recurrency["base"] == "month") {
        let frequency = parseInt(recurrency["frequency"], 10);
        let rule = parseInt(recurrency["rule"], 10);
        let dayOfMonth = parseInt(recurrency["day-of-month"], 10);
        let month = parseInt(recurrency["month"], 10);

        let today = new Date();
        let fromDate = new Date(today.getFullYear(), month, 1);

        let occurrenceDates = [];
        let numberOfOccurrences = parseInt(
            recurrency["number-of-occurrences"],
            10
        );
        let occurrencesEndDate = new Date(recurrency["occurrences-end-date"]);
        let occurrencesNumber = parseInt(recurrency["occurrences-number"], 10);

        let tmpDay;

        if (numberOfOccurrences === 1) {
            // If the number of occurrences is given
            for (let j = 0; j < occurrencesNumber; j++) {
                if (rule === 1) {
                    tmpDay = firstWorkingDayOfMonth(fromDate);
                } else if (rule === 2) {
                    tmpDay = lastWorkingDayOfMonth(fromDate);
                } else if (rule === 3) {
                    tmpDay = exactDayOfMonth(fromDate, dayOfMonth);
                } else if (rule === 4) {
                    tmpDay = firstWorkingDayOnOrAfter(fromDate, dayOfMonth);
                } else if (rule === 5) {
                    tmpDay = lastWorkingDayOnOrBefore(fromDate, dayOfMonth);
                }
                occurrenceDates.push(formatDateLocal(tmpDay));
                fromDate.setMonth(fromDate.getMonth() + frequency);
            }
        } else if (numberOfOccurrences === 2 || numberOfOccurrences === 3) {
            if (numberOfOccurrences === 3) {
                occurrencesEndDate = lastDayInYear(new Date(), 3);
            }
            while (fromDate <= occurrencesEndDate) {
                if (rule === 1) {
                    tmpDay = firstWorkingDayOfMonth(fromDate);
                } else if (rule === 2) {
                    tmpDay = lastWorkingDayOfMonth(fromDate);
                } else if (rule === 3) {
                    tmpDay = exactDayOfMonth(fromDate, dayOfMonth);
                } else if (rule === 4) {
                    tmpDay = firstWorkingDayOnOrAfter(fromDate, dayOfMonth);
                } else if (rule === 5) {
                    tmpDay = lastWorkingDayOnOrBefore(fromDate, dayOfMonth);
                }
                occurrenceDates.push(formatDateLocal(tmpDay));
                fromDate.setMonth(fromDate.getMonth() + frequency);
            }
        }
        console.log("Occurrence dates:", occurrenceDates);
    }
}

// *****************************************************************
// Helper functions
// *****************************************************************

function showHideElement(element, shouldShow) {
    if (element) {
        element.style.display = shouldShow ? "block" : "none";
    }
}

function formatDateLocal(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
}

function firstWorkingDayOfMonth(tmpDate) {
    let year = tmpDate.getFullYear();
    let month = tmpDate.getMonth();
    let date = new Date(year, month, 1);
    while (date.getDay() === 0 || date.getDay() === 6) {
        // 0 = Sunday, 6 = Saturday
        date.setDate(date.getDate() + 1);
    }
    return date;
}

function lastWorkingDayOfMonth(tmpDate) {
    let year = tmpDate.getFullYear();
    let month = tmpDate.getMonth();
    let date = new Date(year, month + 1, 0);
    while (date.getDay() === 0 || date.getDay() === 6) {
        // 0 = Sunday, 6 = Saturday
        date.setDate(date.getDate() - 1);
    }
    return date;
}

function exactDayOfMonth(date, dayOfMonth) {
    const lastDay = getLastDayOfMonth(new Date(date));
    if (lastDay < dayOfMonth) {
        dayOfMonth = lastDay;
    }
    const newDate = new Date(date);
    newDate.setDate(parseInt(dayOfMonth, 10));
    return newDate;
}

function firstWorkingDayOnOrAfter(entryDate, dayOfMonth) {
    let tmpDate = exactDayOfMonth(entryDate, dayOfMonth);
    let date = new Date(tmpDate);
    let tmpMonth = entryDate.getMonth();
    while (date.getDay() === 0 || date.getDay() === 6) {
        date.setDate(date.getDate() + 1);
    }
    if (date.getMonth() > tmpMonth) {
        date = lastWorkingDayOfMonth(new Date(tmpDate));
    }
    return date;
}

// Get the last working day on or before a given date - checked, in use
function lastWorkingDayOnOrBefore(entryDate, dayOfMonth) {
    let tmpDate = exactDayOfMonth(entryDate, dayOfMonth);
    let date = new Date(tmpDate);
    let tmpMonth = entryDate.getMonth();
    while (date.getDay() === 0 || date.getDay() === 6) {
        date.setDate(date.getDate() - 1);
    }
    if (date.getMonth() < tmpMonth) {
        date = firstWorkingDayOfMonth(new Date(tmpDate));
    }
    return date;
}

function lastDayInYear(date, years = 0) {
    const d = new Date(date); // convert string to Date
    const year = d.getFullYear();
    return new Date(year + years, 11, 31); // December 31st of the same year
}

function getLastDayOfMonth(tmpDate) {
    let year = tmpDate.getFullYear();
    let month = tmpDate.getMonth();
    let date = new Date(year, month + 1, 0);
    let day = date.getDate();
    return day;
}

/*
function firstDayOfWeek(date) {
    const day = date.getDay();
    let firstDay;
    if (parseInt(user["first_day_of_week"]) === 0) {
        // If first day of week is Sunday
        firstDay = new Date(date);
        firstDay.setDate(firstDay.getDate() - firstDay.getDay()); // Set to Sunday
    } else if (parseInt(user["first_day_of_week"]) === 1) {
        // If first day of week is Monday
        if (day === 0) {
            date.setDate(date.getDate() - 7);
        }
        firstDay = new Date(date);
        firstDay.setDate(firstDay.getDate() - firstDay.getDay() + 1); // Set to Monday
    } else if (parseInt(user["first_day_of_week"]) === 6) {
        // If first day of week is Saturday
        if (day === 6) {
            date.setDate(date.getDate() + 7);
        }
        firstDay = new Date(date);
        firstDay.setDate(firstDay.getDate() - firstDay.getDay() - 1); // Set to Saturday
    }
    return firstDay;
}
*/

// *****************************************************************
// Event listeners for elements
// *****************************************************************

if (baseElement) {
    baseElement.addEventListener("change", updateFrequencyOptions);
    // updateFrequencyOptions(); // Doesn't need to be called here, because it'll change data what comes from the $recurringData
}

if (frequencyElement) {
    frequencyElement.addEventListener("change", updateRuleOptions);
    // updateRuleOptions(); // it's already called in updateFrequencyOptions
}

if (ruleElement) {
    ruleElement.addEventListener("change", updateVisibilityOfOtherRuleElements);
    updateVisibilityOfOtherRuleElements();
}

if (dayOfWeekElement) {
    dayOfWeekElement.addEventListener("change", getRecurrencyParameters);
}

if (dayOfMonthElement) {
    dayOfMonthElement.addEventListener("change", getRecurrencyParameters);
}

if (monthElement) {
    monthElement.addEventListener("change", getRecurrencyParameters);
}

if (fromDateElement) {
    fromDateElement.addEventListener("change", getRecurrencyParameters);
}

if (numberOfOccurrencesElement) {
    numberOfOccurrencesElement.addEventListener(
        "change",
        updateVisibilityOfOccurrencesElements
    );
    updateVisibilityOfOccurrencesElements();
}

if (occurrencesEndDateElement) {
    occurrencesEndDateElement.addEventListener(
        "change",
        getRecurrencyParameters
    );
}

if (occurrencesNumberElement) {
    occurrencesNumberElement.addEventListener(
        "change",
        getRecurrencyParameters
    );
}

if (dateElement) {
    dateElement.addEventListener("change", getRecurrencyParameters);
}
