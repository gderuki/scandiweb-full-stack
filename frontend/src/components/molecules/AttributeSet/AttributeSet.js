import React, { Component } from 'react';
import './AttributeSet.css';

class AttributeSet extends Component {
  constructor(props) {
    super(props);
    const defaultSelectedAttributes = {};

    this.props.attributeSets.forEach(set => {
      if (set.items && set.items.length > 0) {
        defaultSelectedAttributes[set.id] = set.items[0].value;
      }
    });

    this.state = {
      selectedAttributes: defaultSelectedAttributes,
    };
  }

  componentDidUpdate(prevProps, prevState) {
    if (prevState.selectedAttributes !== this.state.selectedAttributes) {
      console.log('selectedAttributes changed:', this.state.selectedAttributes);
    }
  }

  selectAttribute = (attributeValue, attributeType) => {
    this.setState(prevState => ({
      selectedAttributes: {
        ...prevState.selectedAttributes,
        [attributeType]: attributeValue,
      },
    }));
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
    const { attributeSets } = this.props;
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

export default AttributeSet;