const morgan = require('morgan')
const cors = require('cors')
const express = require('express')
const mongoose = require('mongoose')
const session = require('express-session')
const passport = require('passport')
const methodOverride = require('method-override')
const flash = require('connect-flash')
const path = require('path')
const dotenv = require('dotenv')

// Removed explicit require for Trip at the top
// const Trip = require('./models/trips');
const agencyTripRoutes = require('./routes/agencytrips');

dotenv.config()

// MongoDB connection - MOVED UP
mongoose
    .connect(process.env.CONNECTION_STRING, {
        useNewUrlParser: true,
        useUnifiedTopology: true,
        dbName: 'proj-database',
    })
    .then(() => {
        console.log('Database Connection is ready...')
    })
    .catch((err) => {
        console.log(err)
    })

// Explicitly require all models here after DB connection to ensure they are registered once.
// Ordering by dependencies where possible

require('./models/country') // Country is referenced by Attraction
require('./models/Review') // Review is referenced by Attraction
require('./models/trips') // Trip is referenced by Attraction
require('./models/user') // User might be referenced by other models/reviews
require('./models/location')
require('./models/booking')
require('./models/category')
require('./models/attraction') // Attraction references the above, so require it last


// Removed redundant require for database connection
// require('./config/db')
require('./config/passport')(passport) // initialize Passport strategy

const app = express()

// EJS setup
app.set('view engine', 'ejs')
app.set('views', path.join(__dirname, 'views'))

// Body parser
app.use(express.urlencoded({ extended: true }))
app.use(express.json())

// Static files
app.use(express.static(path.join(__dirname, 'public')))

// Express session
app.use(
    session({
        secret: 'secret_key',
        resave: false,
        saveUninitialized: false,
    })
)

// Passport middleware
app.use(passport.initialize())
app.use(passport.session())

// Flash messages
app.use(flash())
//method-override

const restaurantRoutes = require('./routes/restaurantRoutes')
app.use('/restaurants', restaurantRoutes)

app.use(methodOverride('_method'))

// // Middleware to pass flash messages to all views
// app.use((req, res, next) => {
//     res.locals.success = req.flash('success');
//     res.locals.error = req.flash('error');
//     next();
// });

// app.use((req, res, next) => {
//   res.locals.user = req.user;
//   next();
// });
// Middleware to pass flash messages and user data to all views

app.use((req, res, next) => {
    res.locals.success = req.flash('success')
    res.locals.error = req.flash('error')
    res.locals.user = req.user //  makes `user` available in all EJS views
    next()
})

// Require controllers and set up routes AFTER database connection and app setup
const attractionController = require('./controllers/attractionController')

// Routes
const countryRoutes = require('./routes/countryRoutes')
app.use('/countries', countryRoutes)
const tripRoutes = require('./routes/tripRoutes')
app.use('/trips', tripRoutes)
const bookingRoutes = require('./routes/bookings')
app.use('/bookings', bookingRoutes) // ✅ This is correct


require('./models/trips');
const agencyRoutes = require('./routes/agencyLocations');
app.use('/agency', agencyRoutes);



// One for auth and user routes:
app.use('/', require('./routes/auth'))
app.use('/', require('./routes/users'))
app.use('/profile', require('./routes/profile'))

// Admin-specific:
app.use('/admin', require('./routes/adminusers'))
app.use('/admin/users', require('./routes/adminusers'));
app.use('/admin/trips', require('./routes/admintrips'))
app.use('/admin/locations', require('./routes/adminLocations'))
app.use('/admin/attractions', require('./routes/adminAttractions'))

// Agency-specific:

app.use('/agency', require('./routes/agencyCountryRequests'));
app.use('/agency/trips', require('./routes/agencytrips'));
app.use('/agency/view', require('./routes/agencyViewData'));
app.use('/agency/locations', require('./routes/agencyLocations'));
app.use('/agency/trips', agencyTripRoutes);




app.use('/attractions', require('./routes/attractions'))

// Country management routes
app.use('/admin', require('./routes/admincountries'));

// Category management routes
app.use('/admin/categories', require('./routes/adminCategories'))

// Home route (kept the most comprehensive one)
app.get('/', async (req, res) => {
    try {
        const trips = await mongoose
            .model('Trip')
            .find({ isApproved: true })
            .populate('country')

        // Use controller function to get top attractions
        const attractionsWithAvgRating =
            await attractionController.getTopAttractionsForHomepage()

        res.render('home', {
            trips,
            attractions: attractionsWithAvgRating, // Pass attractions to home.ejs
            user: req.user || null,
            success: req.flash('success'),
            error: req.flash('error'),
        })
    } catch (err) {
        console.error(err)
        res.status(500).send('Server Error')
    }
})

// Removed duplicate home routes
// app.get('/', async (req, res) => {
//   const trips = await Trip.find({ isApproved: true }).populate('country');
//   res.render('home', { user: req.user, trips });
// });

// app.get('/', (req, res) => {
//     res.render('home')
// })

const paymentRoutes = require('./routes/payment')
app.use('/payment', paymentRoutes)


 // Optional, safe to have

// Server
app.listen(3000, () => {
    console.log('Server running on http://localhost:3000')
})

