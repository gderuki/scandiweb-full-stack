import { gql } from '@apollo/client';

export const GET_PRODUCT_DETAILS = gql`  
  query GetProductDetails($id: String!) {
    product(id: $id) {
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