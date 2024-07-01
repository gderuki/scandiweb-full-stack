import { gql } from '@apollo/client';

export const GET_ATTRIBUTE_SETS = gql`  
  query GetAttributes($productId: String!) {
    attributes(productId: $productId) {
      id
      name
      type
      __typename
      items {
        id
        value
        displayValue
        __typename
      }
    }
  }
`;