async function getWeather(city) {
    const apiKey = "ff982e30801c664902181711436705c9";
    const endpoint = `https://api.openweathermap.org/data/2.5/weather?q=${city}&units=metric&lang=ph&appid=${apiKey}`;

    const response = await fetch(endpoint);
    if (response.ok) {
        const data = await response.json();
        return data;
    } else {
        return null;
    }
}

const city = "Baguio City";
const weatherTemperature = document.querySelector('.weather-temp');
const weatherLocation = document.querySelector('.weather-location');
const weatherType = document.querySelector('.weather-type');
const currentDate = new Date();
const currentHour = currentDate.getHours();

getWeather(city)
    .then((weather) => {
        if (weather) {
            weatherLocation.innerHTML = weather.name;
            weatherType.innerHTML = weather.weather[0].description;
            weatherTemperature.innerHTML = weather.main.temp;
        } else {
            console.log("Failed to retrieve weather data.");
        }
    })
    .catch((error) => {
        console.error(error);
    });