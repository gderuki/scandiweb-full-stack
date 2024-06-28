import { gql } from "@apollo/client";

import { ATTRIBUTES_FIELDS } from 'graphql/fragments/AttributesFields';

export const GET_PRODUCTS = gql`
  ${ATTRIBUTES_FIELDS}

  query GetProducts {
    products {
      id
      name
      description
      inStock
      category
      brand
      gallery
      __typename
      prices {
        amount
        __typename
        currency {
            label
            symbol
            __typename
        }
      }
      attributes {
        ...AttributesFields
      }
    }
  }
`;
