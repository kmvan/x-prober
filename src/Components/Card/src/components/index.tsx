import { GUTTER } from '@/Config/src'
import { gettext } from '@/Language/src'
import { NavStore } from '@/Nav/src/stores'
import { device } from '@/Style/src/components/devices'
import { ElevatorNavBody } from '@/Utils/src/components/elevator-nav'
import { observer } from 'mobx-react-lite'
import React from 'react'
import styled from 'styled-components'
import { CardStore } from '../stores'
interface StyleArrowProps {
  isHidden: boolean
}
const StyledFieldset = styled.fieldset`
  position: relative;
  border: 5px solid ${({ theme }) => theme['card.border']};
  border-radius: calc(${GUTTER} * 1.5);
  background: ${({ theme }) => theme['card.bg']};
  margin-bottom: calc(${GUTTER} * 1.5);
  padding: calc(${GUTTER} * 1.5) 0 0;
  box-shadow: ${({ theme }) => theme['card.boxShadow']};
`
const StyledLegend = styled.legend`
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  left: 50%;
  top: 0;
  transform: translate(-50%, -50%);
  background: ${({ theme }) => theme['card.legend.bg']};
  padding: 0.5rem 1rem;
  border-radius: 5rem;
  color: ${({ theme }) => theme['card.legend.fg']};
  margin: 0 auto;
  white-space: nowrap;
`
const StyledBody = styled.div`
  padding: 0 calc(${GUTTER} / 2);
  @media ${device('tablet')} {
    padding: 0 ${GUTTER};
  }
`
const StyleArrow = styled.a<StyleArrowProps>`
  color: ${({ theme }) => theme['card.legend.fg']};
  padding: 0 0.5rem;
  cursor: ${({ isHidden }) => (isHidden ? 'not-allowed' : 'pointer')};
  opacity: ${({ isHidden }) => (isHidden ? '0.1' : '0.5')};
  :active,
  :hover {
    text-decoration: none;
    opacity: ${({ isHidden }) => (isHidden ? '0.1' : '1')};
    color: ${({ theme }) => theme['card.legend.fg']};
  }
`
export const Cards = observer(() => {
  const {
    cardsLength,
    enabledCards,
    enabledCardsLength,
    moveCardDown,
    moveCardUp,
  } = CardStore
  if (!cardsLength) {
    return null
  }
  return (
    <ElevatorNavBody id='innCard' setActiveIndex={NavStore.setActiveIndex}>
      {enabledCards.map(({ id, title, component: Tag }, i) => {
        const upArrow = (
          <StyleArrow
            title={gettext('Move up')}
            isHidden={i === 0}
            onClick={(e) => moveCardUp(e, id)}
            href='#'>
            ▲
          </StyleArrow>
        )
        const downArrow = (
          <StyleArrow
            title={gettext('Move down')}
            isHidden={i === enabledCardsLength - 1}
            onClick={(e) => moveCardDown(e, id)}
            href='#'>
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
    </ElevatorNavBody>
  )
})
