// Custom modules
import { GET_ATTRIBUTE_SETS } from 'graphql/attribute/getAttributeSets';

const AttributeService = {
  fetchAttributeSets: async (client, productId) => {
    try {
      const result = await client.query({
        query: GET_ATTRIBUTE_SETS,
        variables: { productId },
        // Weird bug with Apollo client's caching mechanism.
        // Using composite keys for cache's keyFields configuration doesn't help.
        fetchPolicy: 'network-only',
      });
      return result.data.attributes;
    } catch (error) {
      console.error("Error fetching attributes:", error);
      throw error;
    }
  },

  flattenSelectedAttributes(selectedAttributes) {
    if (Array.isArray(selectedAttributes)) {
      return selectedAttributes.reduce((acc, attrObj) => {
        const [key, value] = Object.entries(attrObj)[0];
        acc[key] = value;
        return acc;
      }, {});
    } else if (typeof selectedAttributes === 'object' && selectedAttributes !== null) {
      return { ...selectedAttributes };
    } else {
      console.error("selectedAttributes is not an array or object:", selectedAttributes);
      return {};
    }
  },

  updateSelectedAttributes(selectedAttributes, attributeValue, attributeType) {
    return {
      ...selectedAttributes,
      [attributeType]: attributeValue,
    };
  },
};

export default AttributeService;