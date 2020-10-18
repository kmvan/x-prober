import React from 'react'
import styled from 'styled-components'
import { GUTTER } from '@/Config/src'
import { rgba } from 'polished'

const StyledSearchLink = styled.a`
  margin: 0 0.2rem 0.2rem 0;
  background: ${({ theme }) => rgba(theme.colorDark, 0.05)};
  padding: 0 0.3rem;
  border-radius: ${GUTTER};
  font-family: consolas;

  :hover {
    background: ${({ theme }) => theme.colorDark};
    color: ${({ theme }) => theme.colorGray};
    text-decoration: underline;
  }
`
const SearchLink = ({ keyword }: { keyword: string }) => {
  return (
    <StyledSearchLink
      href={`https://www.google.com/search?q=php+${encodeURIComponent(
        keyword
      )}`}
      target='_blank'
      rel='nofollow'
    >
      {keyword}
    </StyledSearchLink>
  )
}

export default SearchLink
