import { gql } from "@apollo/client";

export const GET_PRODUCTS = gql`
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
    }
  }
`;
