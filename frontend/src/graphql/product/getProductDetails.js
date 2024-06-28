import { gql } from '@apollo/client';
import { ATTRIBUTES_FIELDS } from 'graphql/fragments/AttributesFields';

export const GET_PRODUCT_DETAILS = gql`
  ${ATTRIBUTES_FIELDS}
  
  query GetProductDetails($id: String!) {
    product(id: $id) {
      id
      name
      description
      inStock
      category
      brand
      prices {
        currency {
          label
          __typename
        }
        amount
        __typename
      }
      gallery
      __typename
      attributes {
        ...AttributesFields
      }
    }
  }
`;