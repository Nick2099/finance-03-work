// *************************************************
// Recurring entry logic
// *************************************************

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

console.log("Available frequencies for base:", recurringMenuNew['base']);
console.log('Frequency translations:', frequencyTranslations);
console.log('Rule translations:', ruleTranslations);
console.log('Weekday translations:', weekdaysTranslations);

// *****************************************************************
// Main logic for updating options and visibility
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
}

// *****************************************************************
// Helper functions
// *****************************************************************

function showHideElement(element, shouldShow) {
    if (element) {
        element.style.display = shouldShow ? "block" : "none";
    }
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