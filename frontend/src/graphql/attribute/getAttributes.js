import { gql } from '@apollo/client';
import { ATTRIBUTES_FIELDS } from 'graphql/fragments/AttributesFields';

export const GET_ATTRIBUTES = gql`
  ${ATTRIBUTES_FIELDS}

  query GetAttributes($id: String!) {
    product(id: $id) {
      attributes {
        ...AttributesFields
      }
    }
  }
`;