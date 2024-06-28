import { gql } from '@apollo/client';

export const GET_ATTRIBUTES = gql`
  query GetAttributes($id: String!) {
    product(id: $id) {
      attributes {
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
  }
`;