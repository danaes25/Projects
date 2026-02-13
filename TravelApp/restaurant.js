const mongoose = require('mongoose')

const restaurantSchema = new mongoose.Schema(
    {
        name: { type: String, required: true },
        description: String,
        cuisine: String,
        imageUrl: String,
        priceRange: String,
        rating: Number,
        trip: { type: mongoose.Schema.Types.ObjectId, ref: 'Trip' },
        location: { type: mongoose.Schema.Types.ObjectId, ref: 'Location' },
        createdBy: {
            type: mongoose.Schema.Types.ObjectId,
            ref: 'User',
            required: true,
        }, // Only agency user
    },
    { timestamps: true }
)

module.exports = mongoose.model('Restaurant', restaurantSchema)
