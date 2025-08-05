function isJson(dataStr) {
  try {
    JSON.parse(dataStr)
    return true
  } catch(e) {
    return false
  }
}

export default isJson