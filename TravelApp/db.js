const mongoose = require('mongoose')

const connectDB = async () => {
    try {
        const uri='mongodb+srv://web_database:web123@cluster0.tp0gscz.mongodb.net/proj-database?retryWrites=true&w=majority&appName=Cluster0&ssl=true';
       console.log('the URI>>>>>>>>>>>>>>>:', uri);
        await mongoose.connect(uri)
        console.log('Database Connection is ready...')
    } catch (err) {
        console.error(err)
    }
}

module.exports = connectDB
