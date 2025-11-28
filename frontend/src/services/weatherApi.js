import axios from "axios";

const API = "http://localhost:8000";

export async function getCountries() {
  const res = await axios.get(`${API}/meta/countries`);
  return res.data;
}

export async function getDateRange() {
  const res = await axios.get(`${API}/meta/date-range`);
  return res.data;
}

export async function fetchWeather(start, end, countries) {
  const params = new URLSearchParams();
  params.append("start", start);
  params.append("end", end);

  countries.forEach(c => params.append("countries[]", c));

  const res = await axios.get(`${API}/weather?${params.toString()}`);
  return res.data;
}
