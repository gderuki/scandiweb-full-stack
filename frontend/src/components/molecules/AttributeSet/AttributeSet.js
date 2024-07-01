// Node modules
import React, { Component } from 'react';

// Custom Modules
import { withApolloClient } from 'hoc/withApolloClient';
import AttributeService from 'services/AttributeService';

// Styles/CSS
import './AttributeSet.css';

class AttributeSet extends Component {
  constructor(props) {
    super(props);

    this.state = {
      selectedAttributes: {},
      attributeSets: [],
    };
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

  componentDidMount() {
    const { apolloClient, productId, selectedAttributes } = this.props;

    AttributeService.fetchAttributeSets(apolloClient, productId)
      .then(attributeSets => {
        this.setState({ attributeSets });
        if (attributeSets.length === 0 && typeof this.props.onAllAttributesSelected === 'function') {
          this.props.onAllAttributesSelected(true);
        }
      });

    if (!selectedAttributes || Object.keys(selectedAttributes).length === 0) {
      return; // exiting, prop not provided
    }

    const flattenedAttributes = AttributeService.flattenSelectedAttributes(selectedAttributes);
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

    const updatedAttributes = AttributeService.updateSelectedAttributes(
      this.state.selectedAttributes,
      attributeValue,
      attributeType
    );

    this.setState({ selectedAttributes: updatedAttributes }, () => {
      if (this.props.onAllAttributesSelected) {
        this.checkAllAttributesSelected();
      }

      if (this.props.onAttributeSelect) {
        this.props.onAttributeSelect(updatedAttributes);
      }
    });
  };

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
}

export default withApolloClient(AttributeSet);