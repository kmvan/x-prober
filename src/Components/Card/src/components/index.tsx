import { GUTTER } from '@/Config/src'
import { gettext } from '@/Language/src'
import { device } from '@/Style/src/components/devices'
import { observer } from 'mobx-react-lite'
import { rgba } from 'polished'
import React from 'react'
import styled from 'styled-components'
import store from '../stores'
interface StyleArrowProps {
  isHidden: boolean
}
const StyledFieldset = styled.fieldset`
  position: relative;
  border: 5px solid #eee;
  border-radius: calc(${GUTTER} * 1.5);
  background: linear-gradient(${rgba('#000', 0.01)}, #fff);
  margin-bottom: calc(${GUTTER} * 1.5);
  padding: calc(${GUTTER} * 1.5) 0 0;
  box-shadow: -1px -1px 0 ${({ theme }) => rgba(theme.colorDarkDeep, 0.1)},
    1px 1px 0 hsla(0, 0%, 100%, 0.5), inset 1px 1px 0 hsla(0, 0%, 100%, 0.5),
    inset -1px -1px 0 ${({ theme }) => rgba(theme.colorDarkDeep, 0.1)};
`
const StyledLegend = styled.legend`
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  left: 50%;
  top: 0;
  transform: translate(-50%, -50%);
  background: ${({ theme }) => theme.colorDark};
  padding: 0.5rem 1rem;
  border-radius: 5rem;
  color: ${({ theme }) => theme.colorGray};
  margin: 0 auto;
  text-shadow: 0 1px 1px ${({ theme }) => theme.colorDark};
  white-space: nowrap;
`
const StyledBody = styled.div`
  padding: 0 calc(${GUTTER} / 2);
  @media ${device('tablet')} {
    padding: 0 ${GUTTER};
  }
`
const StyleArrow = styled.a<StyleArrowProps>`
  color: ${({ theme }) => theme.colorGray};
  padding: 0 0.5rem;
  cursor: ${({ isHidden }) => (isHidden ? 'not-allowed' : 'pointer')};
  opacity: ${({ isHidden }) => (isHidden ? '0.1' : '0.5')};
  :active,
  :hover {
    text-decoration: none;
    opacity: ${({ isHidden }) => (isHidden ? '0.1' : '1')};
    color: ${({ theme }) => theme.colorGray};
  }
`
const Cards = observer(() => {
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
    </>
  )
})
export default Cards
