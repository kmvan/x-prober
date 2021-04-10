import { CardStore } from '@/Card/src/stores'
import { ANIMATION_DURATION_SC, GUTTER } from '@/Config/src'
import { device } from '@/Style/src/components/devices'
import { ElevatorNav } from '@/Utils/src/components/elevator-nav'
import { getElementOffsetTop } from '@/Utils/src/components/get-element-offset-top'
import { observer } from 'mobx-react-lite'
import React, { MouseEvent, ReactElement, useCallback } from 'react'
import styled, { keyframes } from 'styled-components'
import { NavStore } from '../stores'
const slideUp = keyframes`
  from{
    transform: translate3d(0, 100%, 0);
  }
  to{
    transform: translate3d(0, 0, 0);
  }
`
const StyledNav = styled.div`
  position: fixed;
  bottom: 0;
  background: ${({ theme }) => theme['nav.bg']};
  padding: 0 ${GUTTER};
  left: 0;
  right: 0;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  height: 3rem;
  line-height: 3rem;
  overflow-x: auto;
  @media ${device('mobileLg')} {
    overflow-x: unset;
    justify-content: center;
  }
`
const StyledNavLink = styled.a`
  position: relative;
  white-space: nowrap;
  color: ${({ theme }) => theme['nav.fg']};
  padding: 0 0.5rem;
  border-right: 1px solid ${({ theme }) => theme['nav.border']};
  animation: ${slideUp} ${ANIMATION_DURATION_SC}s;
  animation-fill-mode: forwards;
  @media ${device('tablet')} {
    padding: 0 ${GUTTER};
  }
  :hover {
    background: ${({ theme }) => theme['nav.hover.bg']};
    color: ${({ theme }) => theme['nav.hover.fg']};
    text-decoration: none;
  }
  &.active {
    background: ${({ theme }) => theme['nav.active.bg']};
    color: ${({ theme }) => theme['nav.active.fg']};
    text-decoration: none;
  }
  :last-child {
    border-right: 0;
  }
`
const StyledNavLinkTitle = styled.span`
  display: none;
  @media ${device('desktopSm')} {
    display: block;
  }
`
const StyledNavLinkTinyTitle = styled.span`
  display: block;
  @media ${device('desktopSm')} {
    display: none;
  }
`
export const Nav = observer(() => {
  const onClick = useCallback(
    (e: MouseEvent<HTMLAnchorElement>, id: string) => {
      e.preventDefault()
      const target = document.querySelector(`#${id}`) as HTMLElement
      if (!target) {
        return
      }
      history.pushState(null, '', `#${id}`)
      window.scrollTo(0, getElementOffsetTop(target) - 50)
    },
    []
  )
  const items = CardStore.enabledCards
    .map(({ id, title, tinyTitle, enabled = true }) => {
      if (!enabled) {
        return null
      }
      return (
        <StyledNavLink key={id} onClick={(e) => onClick(e, id)} href={`#${id}`}>
          <StyledNavLinkTitle>{title}</StyledNavLinkTitle>
          <StyledNavLinkTinyTitle>{tinyTitle}</StyledNavLinkTinyTitle>
        </StyledNavLink>
      )
    })
    .filter((n) => n) as ReactElement[]
  return (
    <StyledNav>
      <ElevatorNav activeIndex={NavStore.activeIndex}>{items}</ElevatorNav>
    </StyledNav>
  )
})
