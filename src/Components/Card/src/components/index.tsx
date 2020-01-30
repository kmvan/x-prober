import React, { Component } from 'react'
import styled from 'styled-components'
import { observer } from 'mobx-react'
import store from '../stores'
import { COLOR_DARK, GUTTER, COLOR_GRAY } from '~components/Config/src'
import { gettext } from '~components/Language/src'

interface StyleArrowProps {
  isHidden: boolean
}

const StyledFieldset = styled.fieldset`
  position: relative;
  border: 5px solid #eee;
  border-radius: calc(${GUTTER} * 1.5);
  background: linear-gradient(#fff, rgba(255, 255, 255, 0.5));
  margin-bottom: calc(${GUTTER} * 1.5);
  padding: calc(${GUTTER} * 1.5) 0 0;
  box-shadow: -1px -1px 0 rgba(0, 0, 0, 0.1), 1px 1px 0 hsla(0, 0%, 100%, 0.5),
    inset 1px 1px 0 hsla(0, 0%, 100%, 0.5), inset -1px -1px 0 rgba(#000, 0.1);
`

const StyledLegend = styled.legend`
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  left: 50%;
  top: 0;
  transform: translate(-50%, -50%);
  background: ${COLOR_DARK};
  padding: 0.5rem 1rem;
  border-radius: 5rem;
  color: ${COLOR_GRAY};
  margin: 0 auto;
  text-shadow: 0 1px 1px ${COLOR_DARK};
  white-space: nowrap;
`

const StyledBody = styled.div``
const StyleArrow = styled.a<StyleArrowProps>`
  color: ${COLOR_GRAY};
  padding: 0 0.5rem;
  cursor: ${({ isHidden }) => (isHidden ? 'not-allowed' : 'pointer')};
  opacity: ${({ isHidden }) => (isHidden ? '0.1' : '0.5')};
  :hover {
    text-decoration: none;
    opacity: ${({ isHidden }) => (isHidden ? '0.1' : '1')};
    color: ${COLOR_GRAY};
  }
`

@observer
class Cards extends Component {
  public render() {
    const {
      cardsLength,
      enabledCards,
      enabledCardsLength,
      moveCardDown,
      moveCardUp,
    } = store

    if (!cardsLength) {
      return null
    }

    return (
      <>
        {enabledCards.map(({ id, title, component: Tag }, i) => {
          const upArrow = (
            <StyleArrow
              title={gettext('Move up')}
              isHidden={i === 0}
              onClick={() => moveCardUp(id)}
            >
              ▲
            </StyleArrow>
          )

          const downArrow = (
            <StyleArrow
              title={gettext('Move down')}
              isHidden={i === enabledCardsLength - 1}
              onClick={() => moveCardDown(id)}
            >
              ▼
            </StyleArrow>
          )

          return (
            <StyledFieldset key={id} id={id}>
              <StyledLegend>
                {upArrow}
                {title}
                {downArrow}
              </StyledLegend>
              <StyledBody>
                <Tag />
              </StyledBody>
            </StyledFieldset>
          )
        })}
      </>
    )
  }
}

export default Cards
