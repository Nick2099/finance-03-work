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
    recurrency["number-of-occurrences"] = numberOfOccurrencesElement
        ? numberOfOccurrencesElement.value
        : null;
    recurrency["entry-date"] = dateElement ? dateElement.value : null;
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
        let firstDay = firstDayOfWeek(new Date(dateElement.value));
        let startDate = new Date(firstDay);
        let i = 0;
        while (
            startDate.getDay() !== parseInt(dayOfWeekElement.value) &&
            i < 15
        ) {
            startDate.setDate(startDate.getDate() + 1);
            i++;
        }
        startDateElement.value = startDate.toISOString().split("T")[0];

        // calculate end date for weekly recurrency
        let endDate = new Date(startDate);
        let step = parseInt(recurrency["frequency"], 10) * 7; // step in days
        let allDates = [];

        if (parseInt(recurrency["number-of-occurrences"]) == 1) { // case when the number of occurrences is given
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
            console.log("All occurrence dates:", allDates);
        }
    } else if (recurrency["base"] == "month") {
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
