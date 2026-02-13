const mongoose = require('mongoose');


const attractionSchema = new mongoose.Schema({
    name: {
        type: String,
        required: true,
        trim: true
    },
    description: {
        type: String,
        required: true
    },
    location: {
        type: String,
        required: true,
        trim: true
    },
    price: {
        type: Number,
        required: true,
        min: 0
    },
    country: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'Country',
        required: true
    },
    image: {
        type: String, // URL to the image
        required: false // Not strictly required if default image is used
    },
    reviews: [
        {
            type: mongoose.Schema.Types.ObjectId,
            ref: 'Review'
        }
    ],
    trips: [
        {
            type: mongoose.Schema.Types.ObjectId,
            ref: 'Trip'
        }
    ]
}, { timestamps: true });

// Check if the model already exists before compiling it
const Attraction = mongoose.models.Attraction || mongoose.model('Attraction', attractionSchema);

module.exports = Attraction;
