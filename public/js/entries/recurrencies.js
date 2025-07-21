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
const numberOfOccurrencesElement = document.getElementById("number-of-occurrences");
const occurrencesEndDateElement = document.getElementById("occurrences-end-date");
const occurrencesEndDateWrapper = document.getElementById("occurrences-end-date-wrapper");
const occurrencesNumberElement = document.getElementById("occurrences-number");
const occurrencesNumberWrapper = document.getElementById("occurrences-number-wrapper");

// elements needed for output of start and end dates
const dateElement = document.getElementById("date");
const startDateElement = document.getElementById("start-date");
const endDateElement = document.getElementById("end-date");

console.log("Available frequencies for base:", recurringMenuNew['base']);
console.log('Frequency translations:', frequencyTranslations);
console.log('Rule translations:', ruleTranslations);
console.log('Weekday translations:', weekdaysTranslations);

// *****************************************************************
// Main logic for updating options and visibility of elements
// *****************************************************************
function updateFrequencyOptions() {
    if (!baseElement || !frequencyElement) return;

    const selectedBase = baseElement.value;
    frequencyElement.innerHTML = "";

    if (
        recurringMenuNew['base'] &&
        recurringMenuNew['base'][selectedBase] &&
        recurringMenuNew['base'][selectedBase]['frequency']
    ) {
        Object.entries(recurringMenuNew['base'][selectedBase]['frequency']).forEach(
            ([key, opt]) => {
                const option = document.createElement("option");
                option.value = key;
                option.text = frequencyTranslations[opt];
                frequencyElement.appendChild(option);
            }
        );
    }
    updateRuleOptions(); // Update rules based on the new frequency options
}

function updateRuleOptions() {
    if (!frequencyElement || !ruleElement) return;

    const selectedBase = baseElement.value;
    const selectedFrequency = frequencyElement.value;
    ruleElement.innerHTML = "";

    if (
        recurringMenuNew['base'] &&
        recurringMenuNew['base'][selectedBase] &&
        recurringMenuNew['base'][selectedBase]['frequency']
    ) {
        Object.entries(recurringMenuNew['base'][selectedBase]['rule']).forEach(
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
    let elementsToShow = recurringMenuNew['base'][selectedBase]['rule'][selectedRule] || undefined;

    if (elementsToShow === undefined) return;

    if (dayOfMonthElement) {
        showHideElement(dayOfMonthWrapper, elementsToShow['day-of-month']);
    }
    if (dayOfWeekElement) {
        showHideElement(dayOfWeekWrapper, elementsToShow['day-of-week']);
    }
    if (monthElement) {
        showHideElement(monthWrapper, elementsToShow['month']);
    }
    getRuleWithParameters(); // Update the rule with parameters after visibility changes
}

function updateVisibilityOfOccurrencesElements() {
    if (!numberOfOccurrencesElement) return;

    const selectedNumberOfOccurrences = numberOfOccurrencesElement.value;

    const elementsToShow = recurringMenuNew['number-of-occurrences'][selectedNumberOfOccurrences] || undefined;
    if (elementsToShow === undefined) return;
    
    if (occurrencesEndDateElement) {
        showHideElement(occurrencesEndDateWrapper, elementsToShow['date']);
    }
    if (occurrencesNumberElement) {
        showHideElement(occurrencesNumberWrapper, elementsToShow['number']);
    }
    getRuleWithParameters(); // Update the rule with parameters after visibility changes
}

// *****************************************************************
// Helper functions
// *****************************************************************

function showHideElement(element, shouldShow) {
    if (element) {
        element.style.display = shouldShow ? "block" : "none";
    }
}

function getRuleWithParameters() {
    let rule = [];
    
    rule['base'] = baseElement ? baseElement.value : null;
    rule['frequency'] = frequencyElement ? frequencyElement.value : null;
    rule['rule'] = ruleElement ? ruleElement.value : null;
    rule['day-of-month'] = dayOfMonthElement ? dayOfMonthElement.value : null;
    rule['day-of-week'] = dayOfWeekElement ? dayOfWeekElement.value : null;
    rule['month'] = monthElement ? monthElement.value : null;
    rule['number-of-occurrences'] = numberOfOccurrencesElement ? numberOfOccurrencesElement.value : null;
    rule['entry-date'] = dateElement ? dateElement.value : null;
    rule['occurrences-number'] = occurrencesNumberElement ? occurrencesNumberElement.value : null;
    rule['occurrences-end-date'] = occurrencesEndDateElement ? occurrencesEndDateElement.value : null;
    console.log('Rule with parameters:', rule);
    return rule;
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

if (numberOfOccurrencesElement) {
    numberOfOccurrencesElement.addEventListener("change", updateVisibilityOfOccurrencesElements);
    updateVisibilityOfOccurrencesElements();
}   