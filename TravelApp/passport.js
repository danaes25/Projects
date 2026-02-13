const LocalStrategy = require('passport-local').Strategy
const mongoose = require('mongoose')
const bcrypt = require('bcryptjs')
const User = require('../models/user') // Use exactly this everywhere

module.exports = function (passport) {
    passport.use(
        new LocalStrategy(
            { usernameField: 'email' },
            async (email, password, done) => {
                const user = await User.findOne({ email })
                if (!user)
                    return done(null, false, { message: 'No user found' })

                const isMatch = await bcrypt.compare(password, user.password)
                if (!isMatch)
                    return done(null, false, { message: 'Wrong password' })

                return done(null, user)
            }
        )
    )

    passport.serializeUser(async (user, done) => {
        try {
            done(null, user.id)
        } catch (err) {
            done(err)
        }
    })

    passport.deserializeUser(async (id, done) => {
        try {
            const user = await User.findById(id)
            done(null, user)
        } catch (err) {
            done(err, null)
        }
    })
}
