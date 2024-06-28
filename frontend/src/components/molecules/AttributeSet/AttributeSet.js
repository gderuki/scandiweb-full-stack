// Node modules
import React, { Component } from 'react';


// Custom Modules
import { withApolloClient } from 'hoc/withApolloClient';
import { GET_ATTRIBUTES } from 'graphql/attribute/getAttributes';

// Styles/CSS
import './AttributeSet.css';

class AttributeSet extends Component {
  constructor(props) {
    super(props);
    const defaultSelectedAttributes = {};

    this.state = {
      selectedAttributes: defaultSelectedAttributes,
      attributeSets: [],
    };
  }

  componentDidMount() {
    const { apolloClient } = this.props;
    const { productId } = this.props;
    
    apolloClient
      .query({
        query: GET_ATTRIBUTES,
        variables: { id: productId },
      })
      .then(result => this.setState({ attributeSets: result.data.product.attributes }))
      .catch(error => console.error("Error fetching attributes:", error));
  }

  selectAttribute = (attributeValue, attributeType) => {
    this.setState(prevState => {
      const updatedAttributes = {
        ...prevState.selectedAttributes,
        [attributeType]: attributeValue,
      };

      if (this.props.onAttributeSelect) {
        this.props.onAttributeSelect(updatedAttributes);
      }

      return { selectedAttributes: updatedAttributes };
    });
  }

  renderAttributeItem = (attribute, attributeType) => {
    const isSelected = this.state.selectedAttributes[attributeType] === attribute.value;
    const itemClass = isSelected ? " active" : "";

    switch (attribute.__typename) {
      case 'Attribute':
        if (attribute.id === 'Color') {
          return (
            <div
              key={attribute.value}
              className={`item-color${itemClass}`}
              style={{ backgroundColor: attribute.value }}
              onClick={() => this.selectAttribute(attribute.value, attributeType)}
            />
          );
        } else {
          return (
            <div
              key={attribute.value}
              className={`item-text${itemClass}`}
              onClick={() => this.selectAttribute(attribute.value, attributeType)}
            >
              {attribute.displayValue}
            </div>
          );
        }
      default:
        return null;
    }
  }

  renderAttributeSet(attributeSet) {
    return (
      <div key={attributeSet.id} className="item-container">
        {attributeSet.items.map(item => this.renderAttributeItem(item, attributeSet.id))}
      </div>
    );
  }

  render() {
    const { attributeSets } = this.state;
    return (
      <div>
        {attributeSets.map((attributeSet) => (
          <div key={attributeSet.id}>
            <h2 className='attribute-heading'>{attributeSet.name}:</h2>
            {this.renderAttributeSet(attributeSet)}
          </div>
        ))}
      </div>
    );
  }
}

export default withApolloClient(AttributeSet);