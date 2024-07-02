// Node modules
import React, { Component } from 'react';

// Custom Modules
import { withApolloClient } from 'hoc/withApolloClient';
import AttributeService from 'services/AttributeService';
import { toKebabCase } from 'helpers/toKebabCase';

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
    if (attribute.__typename !== 'Attribute') return null;

    const { noClick, small, includeDataTestId } = this.props;
    const isSelected = this.state.selectedAttributes[attributeType] === attribute.value;
    const attributeNameKebab = toKebabCase(attribute.id);
    const attributeValueKebab = toKebabCase(attribute.value);

    const itemClass = [
      attribute.id === 'Color' ? 'item-color' : 'item-text',
      isSelected ? 'active' : '',
      noClick ? 'noclick' : '',
      small ? 'small' : '',
    ].join(' ').trim();

    const dataTestIdBase = `cart-item-attribute-${attributeNameKebab}-${attributeValueKebab}`;
    const dataTestId = isSelected ? `${dataTestIdBase}-selected` : dataTestIdBase;

    const commonProps = {
      className: itemClass,
      onClick: () => this.selectAttribute(attribute.value, attributeType),
      ...(includeDataTestId && {
        'data-testid': dataTestId,
      }),
    };

    return (
      <div
        key={attribute.value}
        {...commonProps}
        style={attribute.id === 'Color' ? { backgroundColor: attribute.value } : {}}
      >
        {attribute.id !== 'Color' && attribute.displayValue}
      </div>
    );
  };

  renderAttributeSet(attributeSet) {
    const { includeDataTestId } = this.props;

    const attributeSetNameKebab = includeDataTestId ? toKebabCase(attributeSet.id) : null;
    const dataTestId = includeDataTestId ? { 'data-testid': `cart-item-attribute-${attributeSetNameKebab}` } : {};

    return (
      <div
        key={attributeSet.id}
        className="item-container"
        {...dataTestId}
      >
        {attributeSet.items.map(item => this.renderAttributeItem(item, attributeSet.id))}
      </div>
    );
  }
}

export default withApolloClient(AttributeSet);