const convertAttributesArrayToObject = (attributesArray) => {
  return attributesArray.reduce((acc, attr) => {
    const [key, value] = Object.entries(attr)[0];
    acc[key] = value;
    return acc;
  }, {});
};

export const generateCompositeKey = (id, attributes) => {
  const attributesString = Object.entries(convertAttributesArrayToObject(attributes))
    .sort((a, b) => a[0].localeCompare(b[0]))
    .map(([key, value]) => `${key}:${value}`)
    .join('|');
  return `${id}|${attributesString}`;
};

export const extractProductIdFromCompositeKey = (compositeKey) => {
  return compositeKey.split('|')[0];
};