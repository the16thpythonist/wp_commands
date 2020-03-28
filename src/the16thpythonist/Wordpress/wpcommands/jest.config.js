const {defaults} = require('jest-config');
module.exports = {
    // ...
    moduleFileExtensions: [...defaults.moduleFileExtensions, 'vue'],
    transform: {
        ".*\\.(vue)$": "vue-jest",
        "^.+\\.js$": "<rootDir>/node_modules/babel-jest"
    },
    transformIgnorePatterns: [
        "!node_modules/"
    ],
    moduleNameMapper: {
        "^@/(.*)$": "<rootDir>/src/$1"
    }
};