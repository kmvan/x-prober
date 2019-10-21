import React, { Component } from 'react'
import styled from 'styled-components'
import { observer } from 'mobx-react'
import store from '../stores'
import { COLOR_DARK, GUTTER } from '~components/Config/src'

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
  position: absolute;
  left: 50%;
  top: 0;
  transform: translate(-50%, -50%);
  background: ${COLOR_DARK};
  padding: 0.5rem 2rem;
  border-radius: 5rem;
  color: #fff;
  margin: 0 auto;
  text-shadow: 0 1px 1px ${COLOR_DARK};
  white-space: nowrap;
`

const StyledBody = styled.div``

@observer
class Cards extends Component {
  public render() {
    const { cardsLength, sortedCards } = store

    if (!cardsLength) {
      return null
    }

    return (
      <>
        {sortedCards.map(({ id, title, component: Tag }) => {
          return (
            <StyledFieldset key={id} id={id}>
              <StyledLegend>{title}</StyledLegend>
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
