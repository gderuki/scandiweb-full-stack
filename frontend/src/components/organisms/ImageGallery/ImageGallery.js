import React from 'react';
import './ImageGallery.css';
import Thumbnails from 'molecules/Thumbnails';
import SquareImage from 'atoms/SquareImage';
import Button from 'atoms/Button';
import PrevItemIcon from 'icons/PrevItemIcon';
import NextItemIcon from 'icons/NextItemIcon';

class ImageGallery extends React.Component {
  state = {
    selectedImageIndex: 0,
    isLightboxOpen: false,
  };

  componentDidMount() {
    document.addEventListener('keydown', this.handleKeyDown);
  }

  componentWillUnmount() {
    document.removeEventListener('keydown', this.handleKeyDown);
  }

  handleKeyDown = (event) => {
    const { selectedImageIndex } = this.state;
    const { images } = this.props;
    if (event.key === 'ArrowLeft') {
      this.setSelectedImageIndex((selectedImageIndex - 1 + images.length) % images.length);
    } else if (event.key === 'ArrowRight') {
      this.setSelectedImageIndex((selectedImageIndex + 1) % images.length);
    }
  };

  setSelectedImageIndex = (index) => {
    this.setState({ selectedImageIndex: index });
  };

  openOverlay = () => {
    const bigImageOpenEvent = new CustomEvent('BIG_IMAGE_OPENED');
    document.dispatchEvent(bigImageOpenEvent);

    this.setState({ isLightboxOpen: true });
  }

  closeOverlay = () => {
    const bigImageCloseEvent = new CustomEvent('BIG_IMAGE_CLOSED');
    document.dispatchEvent(bigImageCloseEvent);

    this.setState({ isLightboxOpen: false });
  };

  render() {
    const { isLightboxOpen } = this.state;
    const { images } = this.props;

    return (
      <div className="image-gallery">
        <Thumbnails
          images={images}
          selectedImageIndex={this.state.selectedImageIndex}
          setSelectedImageIndex={this.setSelectedImageIndex}
        />
        <div className="selected-image">
          {images && images.length > 0 &&
            <SquareImage
              className="image-square"
              imageUrl={images[this.state.selectedImageIndex]}
              onClick={this.openOverlay}
            />
          }
          <Button
            icon={<PrevItemIcon />}
            className='prev-icon pointer'
            onClick={() => this.setSelectedImageIndex((this.state.selectedImageIndex - 1 + images.length) % images.length)}
          />
          <Button
            icon={<NextItemIcon />}
            className='next-icon pointer'
            onClick={() => this.setSelectedImageIndex((this.state.selectedImageIndex + 1) % images.length)}
          />
        </div>
        {isLightboxOpen && (
          <div className="image-overlay" onClick={this.closeOverlay}>
            <SquareImage className="image-full pointer" imageUrl={images[this.state.selectedImageIndex]} />
          </div>
        )}
      </div>
    );
  }
}

export default ImageGallery;