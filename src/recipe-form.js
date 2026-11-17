/*
 * AI: ChatGPT assisted with implementing the dynamic ingredient and step
 * form fields so new fields can be added without reloading the page.
 * This also preserves selected image uploads while editing the form.
 */
const ingredientFields = document.querySelector('#ingredient-fields');
const addIngredientButton = document.querySelector('#add-ingredient');

const stepFields = document.querySelector('#step-fields');
const addStepButton = document.querySelector('#add-step');

addIngredientButton.addEventListener('click', function () {
    const index = ingredientFields.querySelectorAll('.ingredient-fieldset').length;

    const fieldset = document.createElement('fieldset');
    fieldset.className = 'ingredient-fieldset';

    fieldset.innerHTML = `
        <legend>Ingredient ${index + 1}</legend>

        <div class="form-field">
            <label for="ingredient_quantity_${index}">Quantity</label>
            <input
                type="text"
                id="ingredient_quantity_${index}"
                name="ingredient_quantity[]"
                placeholder="e.g. 1 1/2"
            >
        </div>

        <div class="form-field">
            <label for="ingredient_unit_${index}">Unit</label>
            <input
                type="text"
                id="ingredient_unit_${index}"
                name="ingredient_unit[]"
                placeholder="e.g. cups"
            >
        </div>

        <div class="form-field">
            <label for="ingredient_name_${index}">Ingredient</label>
            <input
                type="text"
                id="ingredient_name_${index}"
                name="ingredient_name[]"
                placeholder="e.g. flour"
            >
        </div>
    `;

    ingredientFields.appendChild(fieldset);
});

addStepButton.addEventListener('click', function () {
    const index = stepFields.querySelectorAll('.step-fieldset').length;

    const fieldset = document.createElement('fieldset');
    fieldset.className = 'step-fieldset';

    fieldset.innerHTML = `
        <legend>Step ${index + 1}</legend>

        <div class="form-field">
            <label for="step_title_${index}">Title</label>
            <input
                type="text"
                id="step_title_${index}"
                name="step_title[]"
                placeholder="e.g. Prepare the ingredients"
            >
        </div>

        <div class="form-field">
            <label for="step_instructions_${index}">Instructions</label>
            <textarea
                id="step_instructions_${index}"
                name="step_instructions[]"
                placeholder="Describe what to do in this step."
            ></textarea>
        </div>

        <div class="form-field">
            <label for="step_image_${index}">Step Image</label>
            <input
                type="file"
                id="step_image_${index}"
                name="step_image[]"
                accept=".jpeg,.jpg,.png,.webp"
            >
        </div>
    `;

    stepFields.appendChild(fieldset);
});