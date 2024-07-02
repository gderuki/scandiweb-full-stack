export const toKebabCase = str => {
  if (/^#[0-9A-Fa-f]+$/.test(str)) {
    return str.toLowerCase(); // return the color as is
  }
  
  return str.replace(/([a-z0-9])([A-Z])|(\s+)/g, '$1-$2').toLowerCase();
};