import { gql } from '@apollo/client';

export const ATTRIBUTES_FIELDS = gql`
  fragment AttributesFields on AttributeSet {
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
`;