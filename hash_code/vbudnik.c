/* ************************************************************************** */
/*                                                                            */
/*                                                        :::      ::::::::   */
/*   vbudnik.c                                          :+:      :+:    :+:   */
/*                                                    +:+ +:+         +:+     */
/*   By: vbudnik <marvin@42.fr>                     +#+  +:+       +#+        */
/*                                                +#+#+#+#+#+   +#+           */
/*   Created: 2018/03/01 19:38:29 by vbudnik           #+#    #+#             */
/*   Updated: 2018/03/01 19:51:49 by vbudnik          ###   ########.fr       */
/*                                                                            */
/* ************************************************************************** */

#include <stdio.h>
#include <stdlib.h>


int main ()
{
	int matrix[3][3]={1,0,0,0,0,0,0,0,0}, i, j;
	for (i=0; i<3; i++)
	{
		for(j=0; j<3; j++)
		{
			matrix[i][j] = abs(i-1) + abs(j-1) + 1; //taxicab algorithm
			printf("%d ",matrix[i][j]); //prints the matrix
		}
		printf("\n");
	}
	return (0);
}
