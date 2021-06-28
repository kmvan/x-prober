import React, { FC } from 'react'
import styled from 'styled-components'
import { GUTTER } from '../../../Config/src'
const StyledSearchLink = styled.a`
  margin: 0 0.2rem 0.2rem 0;
  background: ${({ theme }) => theme['search.bg']};
  color: ${({ theme }) => theme['search.fg']};
  padding: 0 0.3rem;
  border-radius: ${GUTTER};
  font-family: consolas;
  :hover {
    text-decoration: underline;
    background: ${({ theme }) => theme['search.hover.bg']};
  }
`
interface SearchLinkProps {
  keyword: string
}
export const SearchLink: FC<SearchLinkProps> = ({ keyword }) => {
  return (
    <StyledSearchLink
      href={`https://www.google.com/search?q=php+${encodeURIComponent(
        keyword
      )}`}
      target='_blank'
      rel='nofollow'>
      {keyword}
    </StyledSearchLink>
  )
}
