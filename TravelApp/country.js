const mongoose = require('mongoose')

const countrySchema = new mongoose.Schema({
    name: { type: String, required: true },
    description: String,images: [String],
    coordinates: {
        lat: Number,
        lng: Number,
    },
    isApproved: { type: Boolean, default: true }, //  Add approval flag
})

module.exports = mongoose.model('Country', countrySchema)
