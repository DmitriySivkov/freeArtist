export default class WaveForm
{
    constructor(data = {}) {
        for (let prop in data) {
            this[prop] = data[prop]
        }
    }

    getValidators(input)
    {
        return Object.values(input.validation)
    }

    validateStep(fields= {}, currentStep)
    {
        return Object.keys(this[currentStep]).find(key => fields[key].valid !== true)
    }
}
