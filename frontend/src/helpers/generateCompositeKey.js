export const generateCompositeKey = (id, attributes) => {
  const attributesString = Object.entries(attributes).sort().map(([key, value]) => `${key}:${value}`).join('|');
  return `${id}|${attributesString}`;
};