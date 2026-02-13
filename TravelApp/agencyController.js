const User = require('../models/user')
const Location = require('../models/location')
const Trip = require('../models/trips')
const Country = require('../models/country')
const Category = require('../models/category')



exports.renderNewLocationForm = async (req, res) => {
  try {
    const countries = await Country.find();
    res.render('agency/locations/new', { countries });
  } catch (err) {
    res.status(500).send('Server error');
  }
};

//  CREATE LOCATION (with city + isTrusted check)

exports.createLocation = async (req, res) => {
  try {
    console.log('Form submitted:', req.body);
    const { name, description, country, city } = req.body;

    if (!name || !country || !city) {
      return res.status(400).json({
        message: 'Name, country, and city are required',
      });
    }

    const user = await User.findById(req.user._id);

    const location = new Location({
      name,
      description,
      country, // ← used to be the country ID
      city,
      createdBy: user._id,
      isApproved: user.isTrusted === true,
    });

    await location.save();

    res.status(201).json({
      message: user.isTrusted
        ? 'Location added and approved'
        : 'Location suggested, awaiting approval',
      location,
    });
  } catch (err) {
    res.status(500).json({ message: 'Server error', error: err.message });
  }
};


//  CREATE TRIP (with price and city)

exports.createTrip = async (req, res) => {
    try {
        const {
            title,
            description,
            country,
            category,
            price,
            locations,
            city,
        } = req.body

        if (!title || !country || !category || !price || !city) {
            return res.status(400).json({
                message:
                    'All fields are required (including title, country, category, price, and city)',
            })
        }

        const trip = new Trip({
            title,
            description,
            country,
            category,
            price,
            city,
            locations: locations || [],
            createdBy: req.user._id,
            isApproved: false,
        })

        await trip.save()

        res.status(201).json({
            message: 'Trip suggested, awaiting approval',
            trip,
        })
    } catch (err) {
        res.status(500).json({
            message: 'Failed to create trip',
            error: err.message,
        })
    }
}


//  GET APPROVED COUNTRIES

exports.getApprovedCountries = async (req, res) => {
    try {
        const countries = await Country.find(); // no filter

        res.json(countries)
    } catch (err) {
        res.status(500).json({ message: 'Server error', error: err.message })
    }
}


//  GET ALL CATEGORIES

exports.getAllCategories = async (req, res) => {
    try {
        const categories = await Category.find()
        res.json(categories)
    } catch (err) {
        res.status(500).json({ message: 'Server error', error: err.message });
    }
}

// Show the new location form


exports.agencyDashboard = async (req, res) => {
  try {
    const tripsListed = await Trip.countDocuments({ createdBy: req.user._id });
    const locationsAdded = await Location.countDocuments({ createdBy: req.user._id });
    const countriesManaged = await Country.countDocuments({ createdBy: req.user._id });

    res.render('dashboards/agencyDashboard', {
      user: req.user,
      tripsListed,
      locationsAdded,
      countriesManaged
    });
  } catch (err) {
    res.status(500).send('Error loading agency dashboard');
  }
};


exports.renderNewTripForm = async (req, res) => {
  const countries = await Country.find();
  const categories = await Category.find();
  res.render('agency/trip/new', { countries, categories });
};
