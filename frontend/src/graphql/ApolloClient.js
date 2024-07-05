// Node modules
import { ApolloClient, InMemoryCache } from '@apollo/client';

const createApolloClient = () => new ApolloClient({
  uri: process.env.API_URL || 'http://164.92.176.118/api/',
  cache: new InMemoryCache({
    typePolicies: {
      Attribute: {
        keyFields: (attribute) => {
          let key = `Attribute:${attribute.id}:${attribute.value}`;
          if (attribute.items) {
            const itemIds = attribute.items.map(item => item.id).join(':');
            key += `:${itemIds}`;
          }
          return key;
        },
      },
    },
  }),
});

export default createApolloClient;