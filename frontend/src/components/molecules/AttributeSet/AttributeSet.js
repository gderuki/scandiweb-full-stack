// Node modules
import React, { Component } from 'react';


// Custom Modules
import { withApolloClient } from 'hoc/withApolloClient';
import { GET_ATTRIBUTE_SETS } from 'graphql/attribute/getAttributeSets';

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
    const { apolloClient, productId, selectedAttributes } = this.props;

    apolloClient
      .query({
        query: GET_ATTRIBUTE_SETS,
        variables: { productId: productId },
        // Weird bug with Apollo client's caching mechanism.
        // Using composite keys for cache's keyFields configuration doesn't help.
        fetchPolicy: 'network-only',
      })
      .then(result => {
        const attributeSets = result.data.attributes;
        this.setState({ attributeSets: attributeSets });
        if (attributeSets.length === 0) {
          if (typeof this.props.onAllAttributesSelected === 'function') {
            this.props.onAllAttributesSelected(true);
          }
        }
      })
      .catch(error => console.error("Error fetching attributes:", error));

    if (!selectedAttributes || Object.keys(selectedAttributes).length === 0) {
      return; // exiting, prop not provided
    }

    const flattenedAttributes = selectedAttributes.reduce((acc, attrObj) => {
      const [key, value] = Object.entries(attrObj)[0];
      acc[key] = value;
      return acc;
    }, {});

    this.setState({
      selectedAttributes: flattenedAttributes,
    }, () => {
      Object.entries(flattenedAttributes).forEach(([attributeType, attributeValue]) => {
        this.selectAttribute(attributeValue, attributeType);
      });
    });
  }

  selectAttribute = (attributeValue, attributeType) => {
    if (this.props.noclick) return;

    const updatedAttributes = {
      ...this.state.selectedAttributes,
      [attributeType]: attributeValue,
    };

    this.setState({ selectedAttributes: updatedAttributes }, () => {
      if (this.props.onAllAttributesSelected) {
        this.checkAllAttributesSelected();
      }

      if (this.props.onAttributeSelect) {
        this.props.onAttributeSelect(updatedAttributes);
      }
    });
  }

  checkAllAttributesSelected = () => {
    const { attributeSets, selectedAttributes } = this.state;

    let allSelected = true;

    attributeSets.forEach(attributeSet => {
      if (!selectedAttributes[attributeSet.id]) {
        allSelected = false;
      }
    });

    if (allSelected) {
      this.props.onAllAttributesSelected(true);
    }
  }

  renderAttributeItem = (attribute, attributeType) => {
    const { noClick, small } = this.props;
    const isSelected = this.state.selectedAttributes[attributeType] === attribute.value;

    const itemClassModifiers = [
      attribute.id === 'Color' ? 'item-color' : 'item-text',
      isSelected ? 'active' : '',
      noClick ? 'noclick' : '',
      small ? 'small' : '',
    ].filter(Boolean);

    const itemClass = itemClassModifiers.join(' ');

    if (attribute.__typename === 'Attribute') {
      if (attribute.id === 'Color') {
        return (
          <div
            key={attribute.value}
            className={itemClass}
            style={{ backgroundColor: attribute.value }}
            onClick={() => this.selectAttribute(attribute.value, attributeType)}
          />
        );
      } else {
        return (
          <div
            key={attribute.value}
            className={itemClass}
            onClick={() => this.selectAttribute(attribute.value, attributeType)}
          >
            {attribute.displayValue}
          </div>
        );
      }
    }
    return null;
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
        {Object.values(attributeSets).map((attributeSet) => (
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