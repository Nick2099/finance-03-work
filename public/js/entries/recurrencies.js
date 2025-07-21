// *************************************************
// Recurring entry logic
// *************************************************

const baseElement = document.getElementById("base");
const frequencyElement = document.getElementById("frequency");
const ruleElement = document.getElementById("rule");
const dayOfMonthElement = document.getElementById("day_of_month");
const dayOfMonthWrapper = document.getElementById("day_of_month-wrapper");
const dayOfWeekElement = document.getElementById("day_of_week");
const dayOfWeekWrapper = document.getElementById("day_of_week-wrapper");
const monthElement = document.getElementById("month");
const monthWrapper = document.getElementById("month-wrapper");
const numberOfOccurrencesElement = document.getElementById("number_of_occurrences");
const occurrencesEndDateElement = document.getElementById("occurrences_end_date");
const occurrencesNumberElement = document.getElementById("occurrences_number");

console.log("Available frequencies for base:", recurringMenuNew['base']);
console.log('Frequency translations:', frequencyTranslations);
console.log('Rule translations:', ruleTranslations);
console.log('Weekday translations:', weekdaysTranslations);

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
    if (!ruleElement) return;
    const selectedBase = baseElement.value;
    const selectedFrequency = frequencyElement.value;
    const selectedRule = ruleElement.value;
    console.log("Selected rule:", selectedRule);

    let elementsToShow = recurringMenuNew['base'][selectedBase]['rule'][selectedRule] || [];
    console.log("Elements to show for rule:", elementsToShow);

    console.log("day_of_month:", elementsToShow['day_of_month']);
    showHideElement(dayOfMonthWrapper, elementsToShow['day_of_month']);
    showHideElement(dayOfWeekWrapper, elementsToShow['day_of_week']);
    showHideElement(monthWrapper, elementsToShow['month']);
}

function showHideElement(element, shouldShow) {
    if (element) {
        element.style.display = shouldShow ? "block" : "none";
    }
}

if (baseElement) {
    baseElement.addEventListener("change", updateFrequencyOptions);
    updateFrequencyOptions(); // Initial call to populate frequency options
}

if (frequencyElement) {
    frequencyElement.addEventListener("change", updateRuleOptions);
    // updateRuleOptions(); // it's already called in updateFrequencyOptions
}

if (ruleElement) {
    ruleElement.addEventListener("change", updateVisibilityOfOtherRuleElements);
    // updateVisibilityOfOtherRuleElements(); // It's already called in updateRuleOptions
}