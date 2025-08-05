import axios from "axios"

const baseURL = "http://127.0.0.1:8000/api"
const request = axios.create({
  baseURL: baseURL
})

async function RequestURL({ url, data = {}, method = "get", ...other } = {}) {
  try {
    const requestData = await request.request({
      url: url,
      data: data,
      method: method,
      ...other
    })
    return requestData
  } catch(e) {
    console.log(e)
    const datares = e.response
    if(datares) {
      return {
        isError: true,
        ...datares
      }
    }
    return {
      isError: true,
      clientError: e.message
    }
  }
}

export default RequestURL